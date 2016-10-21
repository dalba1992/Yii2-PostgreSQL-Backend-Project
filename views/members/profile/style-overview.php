<?php

use yii\helpers\Html;
use app\models\TbAttribute;
use app\models\TbAttributeDetail;
use app\models\TbAttributeDetailImage;
use app\models\CustomerEntityAttribute;
use app\models\CustomerEntityInfo;

$customerEntityInfo = $model->customerEntityInfo;

$TbAttribute = 	TbAttribute::find()->orderBy([
    'id' => SORT_ASC,
])->all();
?>

<div class="row" id="style-section">
    <div class="widget-box">
        <div class="widget-title nopadding">
            <span class="icon"><i class="fa fa-star"></i></span>
            <h5>Style Profile</h5>
        </div>
        <div class="widget-content nopadding inner">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <?php if(sizeof($TbAttribute)>0){
                        foreach($TbAttribute as $attributeinfo){
                            if($attributeinfo->is_active){?>
                                <th><?php echo $attributeinfo->tooltip_bar_title?></th>
                            <?php }
                        }
                    }?>

                    <th>FB (Y/N)</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <?php
                    if(sizeof($TbAttribute)>0){
                        foreach($TbAttribute as $attributeinfo){
                            $style = '';
                            if($attributeinfo->is_active){
                                $customerAttribute = CustomerEntityAttribute::find()->where([
                                    'customer_id' => $_GET['member'],
                                    'attribute_id' => $attributeinfo->id
                                ])->all();

                                ?>
                                <td>
                                    <?php if($attributeinfo->is_multiselect){
                                        if(sizeof($customerAttribute)>0){
                                            foreach($customerAttribute as $attribute){
                                                $TbAttributeDetail = TbAttributeDetail::findOne($attribute->value);
                                                $style = $TbAttributeDetail->title.', '.$style;

                                            }
                                        }
                                    }else{
                                        if(sizeof($customerAttribute)>0){
                                            foreach($customerAttribute as $attribute){
                                                $TbAttributeDetail = TbAttributeDetail::findOne($attribute->value);
                                                $style = $TbAttributeDetail->title;
                                            }
                                        }
                                    }

                                    ?>
                                    <?php echo $style;?>
                                </td>
                            <?php }
                        }
                    }?>
                    <td>
                        <?php
                        //$CustomerEntityInfo = CustomerEntityInfo::find()->where(['customer_id'=>$_GET['member']])->one();
                        //echo 'size->'.sizeof($CustomerEntityInfo);
                        //if(sizeof($CustomerEntityInfo)>0){
                        if($customerEntityInfo){
                            if($customerEntityInfo->fb_token!=null){
                                echo 'Y';
                            }else{
                                echo "N";
                            }
                        }
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>