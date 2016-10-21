<?php

namespace app\controllers;

use app\models\Products;
use app\models\ProductSearch;
use app\models\ProductTags;
use app\models\CustomerEntity;
use app\models\ProductVariants;
use Yii;
use app\components\AController;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\db\QueryBuilder;

use app\modules\services\models\TradegeckoProduct;
use app\modules\services\models\TradegeckoVariant;

use app\modules\services\components\TradeGecko;

class InventoryController extends AController {

    public function actionIndex() {
        return $this->actionList();
    }

    public function actionList($shop = null) {

        $searchModel = new ProductSearch();

        if($shop){
            if($shop == 'concierge')
                $searchModel->shop = 0;
            if($shop == 'shop')
                $searchModel->shop = 1;
        }

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionView($id) {
        $model = $this->loadModel($id);
        $variantQuery = ProductVariants::find()->where(['tradegeckoProductId' => $model->tradegeckoId]);

        $variantDataProvider = new ActiveDataProvider([
            'query' => $variantQuery,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ]
        ]);

        return $this->render('view', [
            'model' => $model,
            'variantDataProvider' => $variantDataProvider
        ]);
    }

    public function actionEdit($id) {
        $model = $this->loadModel($id);
        if(isset($_POST['Products'])) {
            $model->attributes = $_POST['Products'];
            if($model->save()) {
                $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('edit', [
            'model' => $model
        ]);
    }

    public function actionDelete($id) {
        $model = $this->loadModel($id);
        $productId = $model->id;
        if($model->delete()) {
            $variants = ProductVariants::findAll(["productId" => $productId]);
            if(count($variants) > 0) {
                foreach($variants as $variant) {
                    $variant->delete();
                }
            }
            $this->redirect('index');
        }
    }

    public function actionVariantdelete($variantId) {
        $variant = ProductVariants::find()->where(['id' => $variantId])->one();
        $productId = $variant->productId;
        if($variant != null) {
            if($variant->delete()) {
                $this->redirect(['view', 'id' => $productId]);
            }
        }
        throw new HttpException(403, 'Bad request');
    }

    public function actionSync() {
        $startTime = microtime(true);

        // get all variants from TradeGecko
        try{
            $obj1 = new TradeGecko();
            if ($obj1->token == "")
            {
                $ret['code'] = '1';
                $ret['message'] = 'Token was not specified in params.';
                echo json_encode($ret);
                Yii::$app->end();
            }

            $variants = $obj1->getVariants();
            if (isset($variants['type']))
            {
                $ret['code'] = '2';
                $ret['message'] = $variants['message'];
                echo json_encode($ret);
                Yii::$app->end();
            }
        } catch(\Exception $ex) {
            $ret['code'] = '3';
            $ret['message'] = 'Syncing Variants Failed.';
            echo json_encode($ret);
            Yii::$app->end();
        }

        // get all products from TradeGecko
        try{
            $obj = new TradeGecko();
            if ($obj->token == "")
            {
                $ret['code'] = '1';
                $ret['message'] = 'Token was not specified in params.';
                echo json_encode($ret);
                Yii::$app->end();
            }

            $tradeGeckoProducts = $obj->getProducts();
            if (isset($tradeGeckoProducts['type']))
            {
                $ret['code'] = '2';
                $ret['message'] = $tradeGeckoProducts['message'];
                echo json_encode($ret);
                Yii::$app->end();
            }
        } catch(\Exception $ex) {
            $ret['code'] = '3';
            $ret['message'] = 'Syncing Products Failed.';
            echo json_encode($ret);
            Yii::$app->end();
        }

        $tradeGeckoProductIds = array_map(function($product){ return $product['id'];}, $tradeGeckoProducts);

        $results = Products::find()->all();
        $localProducts = [];
        foreach($results as $product)
            $localProducts[$product->tradegeckoId] = $product;

        $localProductIds = array_map(function($product){ return $product->tradegeckoId;}, $localProducts);

        $productKeys = [];
        $products = [];

        // sync the products
        if(count($tradeGeckoProducts)) {
            //die('nu a crapat');

            for ($i = 0; $i < count($tradeGeckoProducts); $i++) {
                $product = $tradeGeckoProducts[$i];

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

                if(!isset($localProducts[$item['tradegeckoId']]))
                    $model = new Products();
                else
                    $model = $localProducts[$item['tradegeckoId']];

                $model->attributes = $item;
                $model->tagNames = [];
                if ($model->save()) {
                    if(isset($product['variant_ids']) && !empty($product['variant_ids'])) {
                        // build the list of variants
                        $variantsArr = [];
                        foreach($product['variant_ids'] as $variantId) {
                            if (isset($variants[$variantId])){
                                $variantsArr[] = $variants['variants'][$variantId];
                            }
                        }
                        $model->syncVariants($variantsArr);
                    }
                }
            }

            // check if a product was deleted from TradeGecko
            $deletableProductIds = array_diff($localProductIds, $tradeGeckoProductIds);

            foreach($deletableProductIds as $productId) {
                $model = Products::find()->where(['tradegeckoId' => $productId])->one();
                if($model) {
                    $model->attributes = [
                        'tradegeckoStatus' => 'inactive',
                        'status' => 'inactive',
                    ];
                    if($model->save()) {
                        if(count($model->variants)) {
                            $model->syncVariants([], true);
                        }
                    }
                }
            }
        } 

        // SYNCING WITH INVENTORY1
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
            if ($product['status'] == 'inactive')
                $itemProduct['product_status'] = 'deleted';

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
                //VARIANTS
                foreach($variants as $variant)
                {
                    $itemVariant = [];
                    $itemVariant['id'] = $variant['tradegeckoId'];
                    $itemVariant['created_at'] = $variant['tradegeckoCreatedAt'];
                    $itemVariant['updated_at'] = $variant['tradegeckoUpdatedAt'];
                    $itemVariant['product_id'] = $variant['tradegeckoProductId'];
                    $itemVariant['default_ledger_account_id'] = '';
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
                    if ($product['status'] == 'inactive')
                        $itemVariant['product_status'] = 'deleted';

                    $itemVariant['product_type'] = $product['productType'];
                    $itemVariant['retail_price'] = $variant['retailPrice'];
                    $itemVariant['sellable'] = $variant['sellable'] == 1 ? 'true' : 'false';
                    $itemVariant['sku'] = $variant['sku'];

                    $itemVariant['variant_status'] = $variant['status'];
                    if ($variant['status'] == 'inactive')
                        $itemVariant['variant_status'] = 'deleted';

                    $itemVariant['stock_on_hand'] = $variant['stockOnHand'].'';
                    $itemVariant['supplier_code'] = $variant['supplierCode'];
                    $itemVariant['taxable'] = $variant['taxable'] == 1 ? 'true' : 'false';
                    $itemVariant['upc'] = $variant['upc'];
                    $itemVariant['wholesale_price'] = $variant['wholesalePrice'];

                    $variantModel = TradegeckoVariant::find()->where(['id' => $variant['tradegeckoId']])->one();
                    if ($variantModel == null)
                        $variantModel = new TradegeckoVariant();

                    $variantModel->attributes = $itemVariant;
                    $variantModel->save();
                }
            }
        }

        $ret['code'] = '0';
        $ret['message'] = 'Successfully Done.';
        echo json_encode($ret);
        Yii::$app->end();
    }

    public function actionSyncOne($inventoryId) {

        $ret = [];
        $startTime = microtime(true);

        // get all variants from TradeGecko
        try{
            $obj1 = new TradeGecko();
            if ($obj1->token == "")
            {
                $ret['code'] = '1';
                $ret['message'] = 'Token was not specified in params.';
                echo json_encode($ret);
                Yii::$app->end();
            }

            $variants = $obj1->getVariants();
            if (isset($variants['type']))
            {
                $ret['code'] = '2';
                $ret['message'] = $variants['message'];
                echo json_encode($ret);
                Yii::$app->end();
            }
        } catch(\Exception $ex) {
            $ret['code'] = '3';
            $ret['message'] = 'Syncing Variants Failed.';
            echo json_encode($ret);
            Yii::$app->end();
        }

        // get all products from TradeGecko
        try{
            $obj = new TradeGecko();
            if ($obj->token == "")
            {
                $ret['code'] = '1';
                $ret['message'] = 'Token was not specified in params.';
                echo json_encode($ret);
                Yii::$app->end();
            }

            $tradeGeckoProduct = $obj->getProduct($inventoryId);
            if (isset($tradeGeckoProduct['type']))
            {
                $ret['code'] = '2';
                $ret['message'] = $tradeGeckoProduct['message'];
                echo json_encode($ret);
                Yii::$app->end();
            }
        } catch(\Exception $ex) {
            $ret['code'] = '3';
            $ret['message'] = 'Syncing a Product Failed.';
            echo json_encode($ret);
            Yii::$app->end();
        }

        $results = Products::find()->all();
        $localProducts = [];
        foreach($results as $product)
            $localProducts[$product->tradegeckoId] = $product;

        $localProductIds = array_map(function($product){ return $product->tradegeckoId;}, $localProducts);

        // sync the products
        if($tradeGeckoProduct != null) {
            $product = $tradeGeckoProduct;

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

            if(!isset($localProducts[$item['tradegeckoId']]))
                $model = new Products();
            else
                $model = $localProducts[$item['tradegeckoId']];

            $model->attributes = $item;
            $model->tagNames = [];
            if ($model->save()) {
                if(isset($product['variant_ids']) && !empty($product['variant_ids'])) {
                    // build the list of variants
                    $variantsArr = [];
                    foreach($product['variant_ids'] as $variantId) {
                        if (isset($variants[$variantId])){
                            $variantsArr[] = $variants[$variantId];
                        }
                    }
                    $model->syncVariants($variantsArr);
                }
            }

        } 

        // SYNCING WITH INVENTORY1
        $product = Products::find()->where(['tradegeckoId' => $inventoryId])->one();
        if ($product != null)
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
            if ($product['status'] == 'inactive')
                $itemProduct['product_status'] = 'deleted';

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
                //VARIANTS
                foreach($variants as $variant)
                {
                    $itemVariant = [];
                    $itemVariant['id'] = $variant['tradegeckoId'];
                    $itemVariant['created_at'] = $variant['tradegeckoCreatedAt'];
                    $itemVariant['updated_at'] = $variant['tradegeckoUpdatedAt'];
                    $itemVariant['product_id'] = $variant['tradegeckoProductId'];
                    $itemVariant['default_ledger_account_id'] = '';
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
                    if ($product['status'] == 'inactive')
                        $itemVariant['product_status'] = 'deleted';

                    $itemVariant['product_type'] = $product['productType'];
                    $itemVariant['retail_price'] = $variant['retailPrice'];
                    $itemVariant['sellable'] = $variant['sellable'] == 1 ? 'true' : 'false';
                    $itemVariant['sku'] = $variant['sku'];
                    
                    $itemVariant['variant_status'] = $variant['status'];
                    if ($variant['status'] == 'inactive')
                        $itemVariant['variant_status'] = 'deleted';

                    $itemVariant['stock_on_hand'] = $variant['stockOnHand'].'';
                    $itemVariant['supplier_code'] = $variant['supplierCode'];
                    $itemVariant['taxable'] = $variant['taxable'] == 1 ? 'true' : 'false';
                    $itemVariant['upc'] = $variant['upc'];
                    $itemVariant['wholesale_price'] = $variant['wholesalePrice'];

                    $variantModel = TradegeckoVariant::find()->where(['id' => $variant['tradegeckoId']])->one();
                    if ($variantModel == null)
                        $variantModel = new TradegeckoVariant();

                    $variantModel->attributes = $itemVariant;
                    $variantModel->save();
                }
            }
        }

        $ret['code'] = '0';
        $ret['message'] = 'Successfully Done.';
        echo json_encode($ret);
        Yii::$app->end();
    }

    /**
     * Filter an array
     * @param array $array
     * @param string $field
     * @param mixed $value
     * @return array
     * @throws InvalidParamException when the $array variable is not an array
     */
    protected function filterByValue($array, $field, $value) {
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

    /**
     * Get model data by primary key
     * @param integer $id model id
     * @return array|null|\yii\db\ActiveRecord
     * @throws HttpException when the model not exists
     */
    protected function loadModel($id) {
        $model = Products::find()->where(['id' => $id])->one();
        if($model) {
            return $model;
        } else {
            throw new HttpException(403, "The request is not valid");
        }
    }

    /**
     * @return array
     * @throws HttpException
     */
    public function actionAjaxProducts() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if(Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $searchModel = new ProductSearch();
            $dataProvider = $searchModel->search($post);

            $attributes = [];
            $customer = CustomerEntity::find()->where(['id' => $post['ProductSearch']['customerId']])->one();
            $customerAttrs = $customer->customerEntityAttributes;
            foreach($customerAttrs as $customerAttribute) {
                $attributes[$customerAttribute->tbAttributeDetail->tb_attribute_id] = [
                    'name' => $customerAttribute->tbAttribute->tooltip_bar_title,
                    'value' => $customerAttribute->tbAttributeDetail->title,
                    'attribute_id' => $customerAttribute->tbAttributeDetail->tb_attribute_id,
                ];
            }

            $customerTopSize = array_filter($attributes, function($attr) {
                return $attr['attribute_id'] == 4;
            })[4]['value'];
            $customerTopSizeTypes = Yii::$app->params['topSizeTypesKeywords'][strtolower($customerTopSize)];

            $customerBottomSize = array_filter($attributes, function($attr) {
                return $attr['attribute_id'] == 5;
            })[5]['value'];

            $topTypes = Yii::$app->params['topTypesKeywords'];
            $bottomTypes = Yii::$app->params['bottomTypesKeywords'];

            $products = [];
            foreach($dataProvider->getModels() as $product) {
                $item = [];
                $item['id'] = $product->id;
                $item['tradegeckoId'] = $product->tradegeckoId;
                $item['name'] = $product->name;
                $item['description'] = $product->description;
                $item['brand'] = $product->brand;
                $item['productType'] = $product->productType;
                $item['supplier'] = $product->supplier;
                $item['quantity'] = $product->quantity;
                $item['status'] = $product->status;
                $item['opt1'] = $product->opt1;
                $item['opt2'] = $product->opt2;
                $item['opt3'] = $product->opt3;
                $item['imageUrl'] = $product->imageUrl;
                $item['tradegeckoStatus'] = $product->tradegeckoStatus;

                $item['variants'] = [];
                if(count($product->variants)) {
                    foreach($product->variants as $variant) {
                        $var = [];
                        $var['id'] = $variant->id;
                        $var['productId'] = $variant->productId;
                        $var['sku'] = $variant->sku;
                        $var['tradegeckoId'] = $variant->tradegeckoId;
                        $var['tradegeckoProductId'] = $variant->tradegeckoProductId;
                        $var['name'] = $variant->name;
                        $var['availableStock'] = $variant->availableStock;
                        $var['stockOnHand'] = $variant->stockOnHand;
                        $var['committedStock'] = $variant->committedStock;
                        $var['retailPrice'] = $variant->retailPrice;
                        $var['opt1'] = $variant->opt1 == null ? '' : $variant->opt1;
                        $var['opt2'] = $variant->opt2 == null ? '' : $variant->opt2;
                        $var['opt3'] = $variant->opt3 == null ? '' : $variant->opt3;

                        array_walk($topTypes, function(&$item, $key, $product) {
                            if(strpos(strtolower($product->productType), $item) !== false) {
                                $product->type = 'top';
                            }
                        }, $product);

                        array_walk($bottomTypes, function(&$item, $key, $product) {
                            if(strpos(strtolower($product->productType), $item) !== false) {
                                $product->type = 'bottom';
                            }
                        }, $product);

                        if($product->type) {
                            if($product->type == 'top') {
                                if(strpos(strtolower($item['opt1']), 'size') !== false) {
                                    $opt = $product->getOpt1();
                                    if(!in_array($customerTopSize, $opt)) {
                                        foreach($customerTopSizeTypes as $size) {
                                            if(!in_array($size, $opt)) {
                                                continue 3;
                                            };
                                        }
                                    }
                                } elseif(strpos(strtolower($item['opt2']), 'size') !== false) {
                                    $opt = $product->getOpt2();
                                    if(!in_array($customerTopSize, $opt)) {
                                        foreach($customerTopSizeTypes as $size) {
                                            if(!in_array($size, $opt)) {
                                                continue 3;
                                            };
                                        }
                                    }
                                } elseif(strpos(strtolower($item['opt3']), 'size') !== false) {
                                    $opt = $product->getOpt3();
                                    if(!in_array($customerTopSize, $opt)) {
                                        foreach($customerTopSizeTypes as $size) {
                                            if(!in_array($size, $opt)) {
                                                continue 3;
                                            };
                                        }
                                    }
                                }

                            } elseif($product->type == 'bottom') {
                                if(strpos(strtolower($item['opt1']), 'size') !== false) {
                                    $opt = $product->getOpt1();
                                    if(!in_array($customerBottomSize, $opt)) {
                                        continue 2;
                                    };
                                } elseif(strpos(strtolower($item['opt2']), 'size') !== false) {
                                    $opt = $product->getOpt2();
                                    if(!in_array($customerBottomSize, $opt)) {
                                        continue 2;
                                    };
                                } elseif(strpos(strtolower($item['opt3']), 'size') !== false) {
                                    $opt = $product->getOpt3();
                                    if(!in_array($customerBottomSize, $opt)) {
                                        continue 2;
                                    };
                                }
                            }
                        }

                        $item['variants'][] = $var;
                    }
                } else {
                    continue;
                }

                $products[] = $item;
            }

            return $products;

        } else {
            throw new HttpException(403, "The request is not valid");
        }
    }

    public function actionToggleShopFlag($id)
    {
        $model = Products::find()->where(['id' => $id])->one();
        if ($model->shop == '0' || $model->shop == NULL)
            $model->shop = '1';
        else
            $model->shop = '0';
        $model->save();

        $tradegeckoModel = TradegeckoProduct::find()->where(['id' => $model->tradegeckoId])->one();
        if ($tradegeckoModel != null)
        {
            $tradegeckoModel->shop = $model->shop;
            $tradegeckoModel->save();
        }

        $this->redirect('index');
    }

}