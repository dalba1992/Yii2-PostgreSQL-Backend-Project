<?php


namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;

/**
 * Class UploadCsvForm
 * @package app\models
 *
 * @property array $data
 */
class UploadCsvForm extends Model {

    /**
     * @var UploadedFile
     */
    public $csvFile;

    /**
     * @var string
     */
    protected $filePath = null;

    /**
     * @var string
     */
    public $fileName = null;

    /**
     * @var array
     */
    protected $header = ['customer_id', 'date', 'item'];

    /**
     * @var array
     */
    protected $_data = [];

    public function rules() {
        return [
            [['csvFile'], 'file', 'skipOnEmpty' => false],
        ];
    }

    public function upload() {
        $this->fileName = "tradegecko_" . time() . "." . $this->csvFile->extension;
        $this->filePath = Yii::getAlias('@webroot') . '/upload/tradegecko_ordercsv/' . $this->fileName;

        if($this->validate()) {
            $this->csvFile->saveAs($this->filePath);
            if(file_exists($this->filePath)) {
                $headerOk = true;
                $fh = fopen($this->filePath,'r');
                $header = fgetcsv($fh);
                foreach($header as $key => $value) {
                    if($key == 0 && !in_array($value, $this->header)) {
                        $headerOk = false;
                    } elseif ($key == 1 && strpos($value, $this->header[1]) === false) {
                        $headerOk = false;
                    } elseif ($key > 1 && strpos($value, $this->header[2]) === false) {
                        $headerOk = false;
                    }
                }
                fclose($fh);
                if(!$headerOk) {
                    unlink($this->filePath);
                    $this->addError('csvFile', 'CSV file format is not valid!');
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get data from csv file
     * @return array
     */
    public function getData() {
        if(file_exists($this->filePath)) {
            $file = fopen($this->filePath, 'r');
            $line = fgetcsv($file);
            $row_id = 0;

            while(($line = fgetcsv($file)) !== false) {
                $row_id++;
                $lines[] = $line;
            }

            $data = [];
            foreach($lines as $lineItem) {
                $item = [];
                foreach($lineItem as $key => $value) {
                    if($key == 0) $item[$this->header[0]] = $value;
                    elseif($key == 1) $item[$this->header[1]] = $value;
                    elseif($key > 1) $item['items'][] = $value;
                }

                $data[] = $item;
            }
            fclose($file);

            return $data;
        } else {
            return [];
        }
    }

    public function attributeLabels() {
        return [
            'csvFile' => 'File'
        ];
    }
}