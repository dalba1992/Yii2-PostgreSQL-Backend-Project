<?php

namespace app\controllers;

use Yii;
use yii\helpers\VarDumper;
use yii\web\Controller;
use app\components\AController;
use app\models\TbAttribute;
use app\models\TbAttributeDetail;
use app\models\TbAttributeDetailImage;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use DirectoryIterator;

class AttributeController extends Controller{

    public function actionIndex() {

        return $this->render('index');
    }


	/**
	 * @Create attribute
	 */ 
    public function actionCreate()
    {
	    $data = Yii::$app->request->post();
		$srcUrl = Yii::$app->params['staticPath'] . Yii::$app->params['orderStepsPath'];
		$AttributeType = null;
        $attribute = new TbAttribute();

        try{
            if(sizeof($data)>0)
            {
                if(isset($data['is_btn']) && $data['is_btn']== "on")
                {
                    $AttributeType =  'is_btn';
                    $attribute->is_btn = TRUE;
                    if(isset($data['is_multiselect']) && $data['is_multiselect']== "on")
                    {
                        $AttributeType =  'is_multiselect';
                        $attribute->is_multiselect = TRUE;
                    }else{
                        $attribute->is_multiselect = FALSE;
                    }
                }else{
                    $attribute->is_btn = FALSE;
                    $attribute->is_multiselect = FALSE;
                }
                if(isset($data['title']) && $data['title'])
                {
                    $attribute->title = $data['title'];
                }
                if(isset($data['tooltip_bar_title']) && $data['tooltip_bar_title'])
                {
                    $attribute->tooltip_bar_title = $data['tooltip_bar_title'];
                }

                $attribute->is_active = TRUE;
                if($attribute->save())
                {
                    $attributeId = $attribute->id;

                    if($AttributeType == null){
                        $form = "f2";
                    }else{
                        $form = "f1";
                    }
                    $i=1;

                    foreach($data[$form] as $order=>$option)
                    {
                        $detail = new TbAttributeDetail();
                        $detail->tb_attribute_id = $attributeId;
                        $detail->title = $option['title'];
                        $detail->description = $option['desc'];
                        $o = str_replace('o', '', $order);
                        if($form == "f1"){
                            $detail->is_image = TRUE;
                        }else{
                            $detail->is_image = FALSE;
                        }
                        $detail->sort_order = $i++;

                        if($detail->save() && $detail->is_image == TRUE)
                        {
                            $image = new TbAttributeDetailImage();
                            $extension = str_replace('image/','',$_FILES[$form]['type'][$order]['file']);
                            $file = "step-".$attributeId."Img".$o.".".$extension;
                            $TmpFile = $_FILES[$form]['tmp_name'][$order]['file'];
                            $TargetFile = $srcUrl.$file;
                            $image->attribute_detail_id = $detail->id;
                            $image->title = $image->image = $file;
                            $image->save();
                            move_uploaded_file($TmpFile, $TargetFile);
                        }
                    }
                }
            }
            Yii::$app->session->setFlash('success',"New Attribute Created Successfully");
            $this->redirect(Url::to(['attribute/index']));
        }catch(Exception $e){
            echo $e->getMessage();
        }

    }
	/**
	 * @Edit attribute detail style
	 */
    public function actionEdit()
    {
        $attributeId = Yii::$app->request->get('id');
        $attribute = null;
        $attributeDetail = null;
        $attributeImages = [];
        $FormType = "form2";
        $srcUrl = Yii::$app->params['staticPath'] . Yii::$app->params['orderStepsPath'];
        if($attributeId !== null)
        {
            $attribute = TbAttribute::find()->where(['id'=>$attributeId])->one();
			
            if(sizeof($attribute)>0)
            {
                if($attribute->is_btn || $attribute->is_multiselect)
                {
                    $FormType = "form1";
                }
                $attributeDetail = TbAttributeDetail::find()->where(['tb_attribute_id'=>$attributeId])
															->orderBy([
																   'id'=>SORT_ASC,
																])->all();
				if(sizeof($attributeDetail)>0)
                {
                    foreach($attributeDetail as $detail)
                    {
                        $ImageDetail = TbAttributeDetailImage::find()->where(['attribute_detail_id'=>$detail->id])->one();
                        if(sizeof($ImageDetail)>0)
                        {
                            $attributeImages[$ImageDetail->attribute_detail_id] = $ImageDetail->attributes;
                        }

                    }

                }
            }else{
				
				Yii::$app->session->setFlash('error',"Attribute Record Not Found");
				Yii::$app->response->redirect(['attribute/index/']);
				return;
			}

			return $this->render('edit',[
                'attribute'=> $attribute,
                'attributeDetail' => $attributeDetail,
                'attributeImages' => $attributeImages,
                'FormType' => $FormType,
                'srcUrl' => $srcUrl,
            ]);
        }else{
            throw new NotFoundHttpException("Page Not Found. ");
        }

    }
	/**
	 * @Update attribute detail style
	 */
    public function actionSave()
    {
		$data = Yii::$app->request->post();
		$srcUrl = Yii::$app->params['staticPath'] . Yii::$app->params['orderStepsPath'];
		$attributeId = Yii::$app->request->get('id');
        $AttributeType = null;
        $attribute = TbAttribute::find()->where(['id'=>$attributeId])->One();

//        VarDumper::dump($_FILES, 10, true);
//        VarDumper::dump($data, 10, true); exit;

        if(sizeof($data)>0 && sizeof($attribute)>0) {
            if(isset($data['title']) && $data['title'])
                $attribute->title = $data['title'];

            if(isset($data['tooltip_bar_title']) && $data['tooltip_bar_title'])
                $attribute->tooltip_bar_title = $data['tooltip_bar_title'];

            if(isset($data['is_multiselect']) && $data['is_multiselect']== true)
                $attribute->is_multiselect = true;
            else
                $attribute->is_multiselect = false;

            if(isset($data['is_btn']) && $data['is_btn']== true)
                $attribute->is_btn = true;
            else
                $attribute->is_btn = false;

            if(isset($data['is_active']) && $data['is_active']== true)
                $attribute->is_active = true;
            else
                $attribute->is_active = false;

            if($attribute->save(false)) {
                $attributeId = $attribute->id;

                if($AttributeType == null){
                    $form = "f2";
                }else{
                    $form = "f1";
                }
                $i=1;


                foreach($data['form'] as $order => $option) {

                    if(isset($option['attr_id'])) {
                        $detail = TbAttributeDetail::findOne($option['attr_id']);
                        if(!$detail) {
                            $detail = new TbAttributeDetail();
                        }
                    } else {
                        $detail = new TbAttributeDetail();
                    }

                    $detail->tb_attribute_id = $attributeId;
                    $detail->title = $option['title'];
                    $detail->description = $option['desc'];
                    $o = str_replace('o','', $order);

                    if(isset($option['is_image']) && $option['is_image'] == 1) {
                        $detail->is_image = true;
                    } else {
                        $detail->is_image = false;
                    }

                    if(isset($option['is_active']) && $option['is_active']==True){
                        $detail->is_active = FALSE;
                    }else{
                        $detail->is_active = TRUE;
                    }
                    $detail->sort_order = (int)$o;

                    if($detail->save(false) && $detail->is_image == true) {
                        $image = TbAttributeDetailImage::find()->where(['attribute_detail_id'=>$detail->id])->one();
                        if($image == null)
                            $image = new TbAttributeDetailImage();

                        $extension = pathinfo($_FILES['form']['name'][$order]['file'], PATHINFO_EXTENSION);
                        if(isset($option['image'])) {
                            $extension = pathinfo($option['image'], PATHINFO_EXTENSION);
                        }
                        $file = "step-".$attributeId."Img".(int)$o.".".$extension;

                        $TmpFile = $_FILES['form']['tmp_name'][$order]['file'];
                        $rename = FALSE;
                        if($TmpFile == "" && isset($option['image'])){
                            $TmpFile = $option['image'];
                            $rename = TRUE;
                        }
                        $TargetFile = $srcUrl.$file;

                        $image->attribute_detail_id = $detail->id;
                        $image->title = $image->image = $file;

                        $image->save(false);

                        if($rename && file_exists($TargetFile)){
                            rename($TmpFile, $TargetFile);
                        }else{
                            move_uploaded_file($TmpFile, $TargetFile);
                        }
                    }
                    $i++;
                }

                Yii::$app->session->setFlash('success',"Attribute Updated Successfully");
                Yii::$app->response->redirect(['attribute/edit','id' => $attributeId]);
            } else {
                Yii::$app->session->setFlash('error',"Failed to update the Attribute Style");
                Yii::$app->response->redirect(['attribute/edit','id' => $attributeId]);
            }
        }
    }
	/**
	 * @Delete detail attribute style 
	 */
	public function actionDeletestyle(){
		$data = Yii::$app->request->post();
        $srcUrl = Yii::$app->params['staticPath'] . Yii::$app->params['orderStepsPath'];
		if(sizeof($data) >0 && isset($data['id']) && $data['id'] > 0){
			try{
				$attributeDetail = TbAttributeDetail::findOne($data['id']);
				if(count($attributeDetail)>0){
                    $attrId = $attributeDetail->id;
                    $image = TbAttributeDetailImage::find()->where(['attribute_detail_id'=>$attrId])->one();
					if($attributeDetail->delete()) {
                        if($image) {
                            $file = $image->image;
                            $TargetFile = $srcUrl.$file;
                            if(file_exists($TargetFile)) {
                                unlink($TargetFile);
                                $image->delete();
                            }
                        }
                    }
					echo 'div_'.$data['id'];
				}
			}catch(Exception $e){
				echo $e->getMessage();
			}
		}
	}
	/**
	 * @Delete attribute
	 */
	public function actionDeleteattribute(){
		if(Yii::$app->request->get('id') && Yii::$app->request->get('id') > 0){
			try{
				$TbAttribute = TbAttribute::findOne(Yii::$app->request->get('id'));
				if(count($TbAttribute)>0){
					$TbAttribute->delete();
					Yii::$app->session->setFlash('success',"Attribute Deleted Successfully");
					Yii::$app->response->redirect(['attribute/index']);
				}
			}catch(Exception $e){
				echo $e->getMessage();
			}
		}
	}
}
