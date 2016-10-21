<?php

namespace app\models;

use dosamigos\taggable\Taggable;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\Query;
use yii\db\QueryBuilder;
use yii\helpers\VarDumper;

use app\modules\services\components\TradeGecko;

/**
 * This is the model class for table "product".
 *
 * @property integer $id
 * @property integer $tradegeckoId
 * @property string $name
 * @property string $description
 * @property string $brand
 * @property string $productType
 * @property string $supplier
 * @property string $quantity
 * @property string $status
 * @property string $opt1
 * @property string $opt2
 * @property string $opt3
 * @property string $imageUrl
 * @property array $tagNames
 * @property string $tradegeckoCreatedAt
 * @property string $tradegeckoUpdatedAt
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $shop
 */
class Products extends \yii\db\ActiveRecord
{
    public static $topTypes = [
        'shirt',
        'top',
        'button',
        'jacket',
    ];

    public static $bottomTypes = [
        'bottom',
        'pants',
        'shorts',
        'jeans'
    ];

    private $_type;

    /**
     * Tags
     * @var array
     */
    public $tagNames = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
                'value' => new Expression('NOW()')
            ],
//            [
//                'class' => Taggable::className()
//            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tradegeckoId'], 'integer'],
            [['name', 'productType', 'supplier', 'quantity', 'status'], 'required'],
            [['description', 'shop'], 'string'],
            [['quantity'], 'number'],
            [['tradegeckoCreatedAt', 'tradegeckoUpdatedAt', 'createdAt', 'updatedAt', 'tagNames'], 'safe'],
            [['name', 'brand', 'supplier'], 'string', 'max' => 200],
            [['productType', 'opt1', 'opt2', 'opt3', 'imageUrl'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 30],
            [['tagNames'], 'safe']
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags() {
        return $this->hasMany(ProductTags::className(), ['id' => 'tagId'])->viaTable('producttagassign', ['productId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVariants() {
        return $this->hasMany(ProductVariants::className(), ['productId' => 'id']);
    }

    public function syncVariants($variants = [], $productDeleted = false) {
        if($productDeleted) {
            $variants = $this->variants;

            if(count($variants)) {
                foreach($variants as $variant) {
                    if($variant) {
                        $variant->attributes = [
                            'availableStock' => 0,
                            'stockOnHand' => 0,
                            'committedStock' => 0,
                            'incomingStock' => 0
                        ];
                        $variant->save(false);
                    }
                }
            }
        }
        else {
            if(!empty($variants)) {
                $queryBuilder = new QueryBuilder(Yii::$app->db);

                // sql query to create the temporary
                $createTempTableQuery = 'CREATE TEMPORARY TABLE productvarianttemp(LIKE "productvariant" INCLUDING ALL) ON COMMIT DROP;';

                $itemKeys = [];
                $items = [];
                $deletableVariants = [0];

                foreach ($variants as $variant) {
                    if ($variant != null) {

                        $deletableVariants[] = $variant['id'];

                        $item = [];
                        $item['tradegeckoId'] = $variant['id'];
                        $item['productId'] = $this->id;
                        $item['tradegeckoProductId'] = $variant['product_id'];
                        $item['name'] = $variant['name'];
                        $item['sku'] = $variant['sku'];
                        $item['description'] = $variant['description'];
                        $item['supplierCode'] = $variant['supplier_code'];
                        $item['upc'] = $variant['upc'];
                        $item['buyPrice'] = $variant['buy_price'];
                        $item['wholesalePrice'] = $variant['wholesale_price'];
                        $item['retailPrice'] = $variant['retail_price'];
                        $item['lastCostPrice'] = $variant['last_cost_price'];
                        $item['movingAverageCost'] = $variant['moving_average_cost'];
                        $item['availableStock'] = (float)$variant['stock_on_hand'] + (float)$variant['incoming_stock'] - (float)$variant['committed_stock'];
                        $item['stockOnHand'] = $variant['stock_on_hand'];
                        $item['committedStock'] = $variant['committed_stock'];
                        $item['incomingStock'] = $variant['incoming_stock'];
                        $item['opt1'] = $variant['opt1'];
                        $item['opt2'] = $variant['opt2'];
                        $item['opt3'] = $variant['opt3'];
                        $item['status'] = $variant['status'];
                        $item['taxable'] = $variant['taxable'] == true ? 1 : 0;
                        $item['sellable'] = $variant['sellable'] == true ? 1 : 0;
                        $item['tradegeckoCreatedAt'] = $variant['created_at'];
                        $item['tradegeckoUpdatedAt'] = $variant['updated_at'];

                        if(isset($variant['image_ids']) && count($variant['image_ids']) > 0) {
                            /*foreach($variant['image_ids'] as $imageId) {
                                $imgObj = new Tradegecko;
                                $image = $imgObj->getImage($imageId);
                                $model = ProductImage::find()->where(['tradegeckoId' => $imageId])->one();
                                if($model == null) {
                                    $model = new ProductImage();
                                    $model->tradegeckoId = $image['id'];
                                    $model->tradegeckoVariantId = $variant['id'];
                                    $model->uploaderId = $image['uploader_id'];
                                    $model->name = $image['name'];
                                    $model->position = $image['position'];
                                    $model->basePath = $image['base_path'];
                                    $model->fileName = $image['file_name'];
                                    $model->versions = json_encode($image['versions']);
                                    $model->imageProcessing = $image['image_processing'] == true ? 1 : 0;

                                    $model->save(false);
                                }
                            }*/
                        }

                        $itemKeys = array_keys($item);
                        $items[] = $item;
                    }
                }
                $batchQuery = $queryBuilder->batchInsert('productvarianttemp', $itemKeys, $items).';';

                // this query insert a variant or if exists will be update
                $updateInsertQuery = '
                    WITH upd AS (
                        UPDATE productvariant o
                            SET "name" = "t"."name",
                                "sku" = "t"."sku",
                                "description" = "t"."description",
                                "supplierCode" = "t"."supplierCode",
                                "upc" = "t"."upc",
                                "buyPrice" = "t"."buyPrice",
                                "wholesalePrice" = "t"."wholesalePrice",
                                "retailPrice" = "t"."retailPrice",
                                "lastCostPrice" = "t"."lastCostPrice",
                                "movingAverageCost" = "t"."movingAverageCost",
                                "availableStock" = "t"."availableStock",
                                "stockOnHand" = "t"."stockOnHand",
                                "committedStock" = "t"."committedStock",
                                "incomingStock" = "t"."incomingStock",
                                "opt1" = "t"."opt1",
                                "opt2" = "t"."opt2",
                                "opt3" = "t"."opt3",
                                "status" = "t"."status",
                                "taxable" = "t"."taxable",
                                "sellable" = "t"."sellable",
                                "tradegeckoCreatedAt" = "t"."tradegeckoCreatedAt",
                                "tradegeckoUpdatedAt" = "t"."tradegeckoUpdatedAt"
                            FROM productvarianttemp t
                        WHERE "o"."tradegeckoId" = "t"."tradegeckoId"
                      RETURNING "o"."id"
                    )
                    INSERT INTO productvariant
                            (
                                "id"
                                ,"tradegeckoId"
                                ,"tradegeckoProductId"
                                ,"name"
                                ,"sku"
                                ,"description"
                                ,"supplierCode"
                                ,"upc"
                                ,"buyPrice"
                                ,"wholesalePrice"
                                ,"retailPrice"
                                ,"lastCostPrice"
                                ,"movingAverageCost"
                                ,"availableStock"
                                ,"stockOnHand"
                                ,"committedStock"
                                ,"incomingStock"
                                ,"opt1"
                                ,"opt2"
                                ,"opt3"
                                ,"status"
                                ,"taxable"
                                ,"sellable"
                                ,"tradegeckoCreatedAt"
                                ,"tradegeckoUpdatedAt"
                                ,"productId"
                            )
                        SELECT "id"
                            ,"tradegeckoId"
                            ,"tradegeckoProductId"
                            ,"name"
                            ,"sku"
                            ,"description"
                            ,"supplierCode"
                            ,"upc"
                            ,"buyPrice"
                            ,"wholesalePrice"
                            ,"retailPrice"
                            ,"lastCostPrice"
                            ,"movingAverageCost"
                            ,"availableStock"
                            ,"stockOnHand"
                            ,"committedStock"
                            ,"incomingStock"
                            ,"opt1"
                            ,"opt2"
                            ,"opt3"
                            ,"status"
                            ,"taxable"
                            ,"sellable"
                            ,"tradegeckoCreatedAt"
                            ,"tradegeckoUpdatedAt"
                            ,"productId"
                         FROM productvarianttemp t LEFT JOIN upd u USING(id)
                        WHERE u.id IS NULL
                    GROUP BY t.id;
                ';
                $sql = $createTempTableQuery . $batchQuery . $updateInsertQuery;

                // remove all duplicate variants
                ProductVariants::deleteAll(['in', 'tradegeckoId', $deletableVariants]);

                // do the magic and insert all the new variants
                Yii::$app->db->pdo->exec($sql);
            }
        }
    }

    /**
     * Get product types from all products
     * @return array
     */
    public static function productTypes() {
        $products = self::find()->all();
        $productTypes = [];

        foreach($products as $product) {
            $productTypes[$product->productType] = $product->productType;
        }

        return $productTypes;
    }

    /**
     * Get suppliers from all products
     * @return array
     */
    public function getSuppliers() {
        $products = self::find()->all();
        $suppliers = [];

        foreach($products as $product) {
            $suppliers[$product->supplier] = $product->supplier;
        }

        return $suppliers;
    }

    /**
     * Get all the brands
     * @return array
     */
    public function getBrands() {
        $products = self::find()->all();
        $brands = [];

        foreach($products as $product) {
            $brands[$product->brand] = $product->brand;
        }

        return $brands;
    }

    public function setType($newType) {
        $this->_type = $newType;
    }
    public function getType() {
        if($this->_type) {
            return $this->_type;
        }
        return null;
    }

    public function getOpt1() {
        $opt = [];
        if($this->variants && $this->opt1) {
            foreach($this->variants as $variant) {
                $option = $variant->opt1;
                if(is_string($option)) {
                    $option = strtolower($option);
                }
                $opt[] = $option;
            }
        }

        return $opt;
    }

    public function getOpt2() {
        $opt = [];
        if($this->variants && $this->opt2) {
            foreach($this->variants as $variant) {
                $option = $variant->opt2;
                if(is_string($option)) {
                    $option = strtolower($option);
                }
                $opt[] = $option;
            }
        }

        return $opt;
    }

    public function getOpt3() {
        $opt = [];
        if($this->variants && $this->opt3) {
            foreach($this->variants as $variant) {
                $option = $variant->opt3;
                if(is_string($option)) {
                    $option = strtolower($option);
                }
                $opt[] = $option;
            }
        }

        return $opt;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tradegeckoId' => 'Tradegecko ID',
            'name' => 'Name',
            'description' => 'Description',
            'brand' => 'Brand',
            'productType' => 'Product Type',
            'supplier' => 'Supplier',
            'quantity' => 'Quantity',
            'status' => 'Status',
            'opt1' => 'Opt1',
            'opt2' => 'Opt2',
            'opt3' => 'Opt3',
            'imageUrl' => 'Image Url',
            'tagNames' => 'Tags',
            'tradegeckoCreatedAt' => 'Tradegecko Created At',
            'tradegeckoUpdatedAt' => 'Tradegecko Updated At',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }
}
