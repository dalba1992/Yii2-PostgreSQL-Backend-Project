<?php
namespace app\commands;

use app\models\Products;
use Yii;
use yii\base\Exception;
use yii\base\ExitException;
use yii\base\InvalidParamException;
use yii\console\Controller;
use yii\base\ErrorException;

use app\components;
use app\models\CustomerEntityStatus;
use yii\base\Component;

use app\models\CustomerEntity;
use app\models\CustomerSubscription;
use app\models\CustomerEntityAddress;
use app\models\Product;
use app\models\Brand;
use app\models\Category;
use app\models\Productattribute;
use app\models\Productattributeoption;
use app\models\Productattributevalue;
use app\models\Variants;
use app\models\ProductVariants;
use app\models\Variantsimage;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

use app\modules\services\models\TradegeckoProduct;
use app\modules\services\models\TradegeckoVariant;

class TradeGeckoController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        Yii::$app->utils->logToConsole($message);
    }

    public function actionSync() {

        $startTime = microtime(true);

        // get all variants from TradeGecko 7020922
        $variantsDataProvider = Yii::$app->tradeGecko->getVariantsDataProvider();
        $variants = $variantsDataProvider->allModels;

        // get all products from TradeGecko
        $productsDataProvider = Yii::$app->tradeGecko->getProductsDataProvider();
        $products = $productsDataProvider->allModels;


        $models = Products::find()->all();
        $dbProductIds = [];
        foreach($models as $model) {
            $dbProductIds[] = $model->tradegeckoId;
        }

        // sync the products
        if(count($products)) {
            $tradegeckoProductIds = [];
            for ($i = 0; $i < count($products); $i++) {
                $product = $products[$i];

                $tradegeckoProductIds[] = $product['id'];

                $item = [];
                $item['tradegeckoId'] = $product['id'];
                $item['name'] = $product['name'];
                $item['description'] = $product['description'];
                $item['brand'] = $product['brand'];
                $item['productType'] = $product['product_type'];
                $item['supplier'] = $product['supplier'];
                $item['quantity'] = $product['quantity'];
                $item['status'] = $product['status'];
                $item['opt1'] = $product['opt1'];
                $item['opt2'] = $product['opt2'];
                $item['opt3'] = $product['opt3'];
                $item['imageUrl'] = $product['image_url'];
                $item['tradegeckoCreatedAt'] = $product['created_at'];
                $item['tradegeckoUpdatedAt'] = $product['updated_at'];
                $item['tradegeckoStatus'] = 'normal';

                $model = Products::find()->where(['tradegeckoId' => $item['tradegeckoId']])->one();
                if($model === null) {
                    $model = new Products();
                }
                $model->attributes = $item;
                $model->tagNames = $product['tags'];
                if ($model->save()) {
                    if(isset($product['variant_ids']) && count($product['variant_ids'])) {
                        $variantsArr = [];
                        foreach($product['variant_ids'] as $variant_id) {
                            $variant = $this->filterByValue($variants, "id", $variant_id);
                            $variantsArr[] = $variant;
                        }
                        $model->syncVariants($variantsArr);
                    }
                    echo "Product sync: " . $model->name . "\n";
                }
            }

            // check if a product was deleted from TradeGecko
            foreach($dbProductIds as $product_id) {
                if(!in_array($product_id, $tradegeckoProductIds)) {
                    $model = Products::find()->where(['tradegeckoId' => $product_id])->one();
                    if($model) {
                        $model->attributes = [
                            'quantity' => 0,
                            'tradegeckoStatus' => 'deleted'
                        ];
                        if($model->save()) {
                            if(count($model->variants)) {
                                $model->syncVariants([], true);
                            }
                            echo "Product " . $model->name . " was deleted from TradeGecko"."\n";
                        }
                    }
                }
            }

            $finishTime = microtime(true) - $startTime;

            echo $finishTime . ' seconds'."\n";
        } else {
            echo "No items were found"."\n";
            return 1;
        }

        return 0;
    }

    /**
     * Filter an array
     * @param array $array
     * @param string $field
     * @param mixed $value
     * @return array
     * @throws InvalidParamException when the $array variable is not an array
     */
    public function filterByValue($array, $field, $value) {
        if(is_array($array) && count($array) > 0) {
            $newArr = [];
            foreach(array_keys($array) as $key) {
                $temp[$key] = $array[$key][$field];
                if($temp[$key] == $value) {
                    $newArr = $array[$key];
                }
            }
            return $newArr;
        } else {
            throw new InvalidParamException();
        }
    }

    public function actionListCompanies()
    {
        $accesstoken = "Bearer ".Yii::$app->params['tradegecko']['token'];

        $header = array();
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: '.$accesstoken;

        $url = components\TradeGeckoHelper::TG_API_COMPANIES_URL;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($ch);
        curl_close($ch);

        $string = json_decode($response, true);
        echo VarDumper::dumpAsString($string);
    }

    public function actionSyncDb()
    {
        $realArr = [];
        $realArr['synced_product'] = 0;
        $realArr['synced_variant'] = 0;
        $realArr['synced_image'] = 0;

        $storedArr = [];
        $storedArr['synced_product'] = 0;
        $storedArr['synced_variant'] = 0;
        $storedArr['synced_image'] = 0;

        //SYNCING WITH INVENTORY1
        $products = Products::find()->all();
        foreach($products as $product)
        {
            $variants = ProductVariants::find()->where(['tradegeckoProductId'=>$product['tradegeckoId']])->all();

            $itemProduct = [];
            $itemProduct['id'] = $product['tradegeckoId'];
            $itemProduct['product_name'] = $product['name'];
            $itemProduct['created_at'] = $product['tradegeckoCreatedAt'];
            $itemProduct['image_url'] = $product['imageUrl'];
            $itemProduct['opt1'] = $product['opt1'];
            $itemProduct['opt2'] = $product['opt2'];
            $itemProduct['opt3'] = $product['opt3'];
            $itemProduct['product_type'] = $product['productType'];
            $itemProduct['search_cache'] = '';
            $itemProduct['product_status'] = $product['status'];
            $itemProduct['tags'] = '';
            $itemProduct['variant_ids'] = '';
            $itemProduct['description'] = $product['description'];
            $itemProduct['supplier'] = $product['supplier'];
            $itemProduct['brand'] = $product['brand'];
            $itemProduct['updated_at'] = $product['tradegeckoUpdatedAt'];
            $itemProduct['quantity'] = $product['quantity'];
            $itemProduct['vendor'] = '';
            $itemProduct['supplier_ids'] = '';
            $itemProduct['variant_num'] = count($variants);
            $itemProduct['shop'] = $product['shop'];

            $model = TradegeckoProduct::find()->where(['id' => $product['tradegeckoId']])->one();
            if ($model == null)
                $model = new TradegeckoProduct();

            $model->attributes = $itemProduct;
            if ($model->save()) {
                $storedArr['synced_product'] ++;
                //VARIANTS
                foreach($variants as $variant)
                {
                    $itemVariant = [];
                    $itemVariant['id'] = $variant['tradegeckoId'];
                    $itemVariant['created_at'] = $variant['tradegeckoCreatedAt'];
                    $itemVariant['updated_at'] = $variant['tradegeckoUpdatedAt'];
                    $itemVariant['product_id'] = $variant['tradegeckoProductId'];
                    $itemVariant['default_ledger_account_id'] = '';//$variant['default_ledger_account_id'];
                    $itemVariant['buy_price'] = $variant['buyPrice'];
                    $itemVariant['committed_stock'] = $variant['committedStock'].'';
                    $itemVariant['incoming_stock'] = $variant['incomingStock'].'';
                    $itemVariant['composite'] = '';
                    $itemVariant['description'] = $variant['description'];
                    $itemVariant['is_online'] = 'false';
                    $itemVariant['keep_selling'] = 'false';
                    $itemVariant['last_cost_price'] = $variant['lastCostPrice'];
                    $itemVariant['moving_average_cost'] = $variant['movingAverageCost'];
                    $itemVariant['variant_name'] = $variant['name'];
                    $itemVariant['opt1'] = $variant['opt1'];
                    $itemVariant['opt2'] = $variant['opt2'];
                    $itemVariant['opt3'] = $variant['opt3'];
                    $itemVariant['product_name'] = $product['name'];
                    $itemVariant['product_status'] =$product['status'];
                    $itemVariant['product_type'] = $product['productType'];
                    $itemVariant['retail_price'] = $variant['retailPrice'];
                    $itemVariant['sellable'] = $variant['sellable'] == 1 ? 'true' : 'false';
                    $itemVariant['sku'] = $variant['sku'];
                    $itemVariant['variant_status'] = $variant['status'];
                    $itemVariant['stock_on_hand'] = $variant['stockOnHand'].'';
                    $itemVariant['supplier_code'] = $variant['supplierCode'];
                    $itemVariant['taxable'] = $variant['taxable'] == 1 ? 'true' : 'false';
                    $itemVariant['upc'] = $variant['upc'];
                    $itemVariant['wholesale_price'] = $variant['wholesalePrice'];

                    $variantModel = TradegeckoVariant::find()->where(['id' => $variant['tradegeckoId']])->one();
                    if ($variantModel == null)
                        $variantModel = new TradegeckoVariant();

                    $variantModel->attributes = $itemVariant;
                    if ($variantModel->save()) {
                        $storedArr['synced_variant'] ++;
                    }
                    $realArr['synced_variant'] ++;
                }

            }
            $realArr['synced_product'] ++;
        }

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Product\t\t".$realArr['synced_product']."\t".$storedArr['synced_product'].PHP_EOL;
        echo "Variant\t\t".$realArr['synced_variant']."\t".$storedArr['synced_variant'].PHP_EOL;
        echo "Image\t\t".$realArr['synced_image']."\t".$storedArr['synced_image'].PHP_EOL.PHP_EOL;
    }
}
