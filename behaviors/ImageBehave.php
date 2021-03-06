<?php
namespace infoweb\cms\behaviors;

use yii;
use yii\helpers\BaseFileHelper;
use yii\web\UploadedFile;
use yii\helpers\StringHelper;

use infoweb\cms\models\Image;
use infoweb\cms\models\ImageUploadForm;

class ImageBehave extends \rico\yii2images\behaviors\ImageBehave
{
    /**
     *
     * Method copies image file to module store and creates db record.
     *
     * @param $absolutePath
     * @param bool $isFirst
     * @return bool|Image
     * @throws \Exception
     */
    public function attachImage($absolutePath, $isMain = false)
    {
        if(!preg_match('#http#', $absolutePath)){
            if (!file_exists($absolutePath)) {
                throw new \Exception('File not exist! :'.$absolutePath);
            }
        }else{
            //nothing
        }

        if (!$this->owner->primaryKey) {
            throw new \Exception('Owner must have primaryKey when you attach image!');
        }

        $pictureFileName = basename($absolutePath);

        $pictureSubDir = $this->getModule()->getModelSubDir($this->owner);
        $storePath = $this->getModule()->getStorePath($this->owner);

        $newAbsolutePath = $storePath .
            DIRECTORY_SEPARATOR . $pictureSubDir .
            DIRECTORY_SEPARATOR . $pictureFileName;

        BaseFileHelper::createDirectory($storePath . DIRECTORY_SEPARATOR . $pictureSubDir,
            0775, true);

        copy($absolutePath, $newAbsolutePath);

        if (!file_exists($newAbsolutePath)) {
            throw new \Exception('Cant copy file! ' . $absolutePath . ' to ' . $newAbsolutePath);
        }

        $image = new Image;
        $image->itemId = $this->owner->primaryKey;
        $image->filePath = $pictureSubDir . '/' . $pictureFileName;
        $image->modelName = $this->getModule()->getShortClass($this->owner);

        $image->urlAlias = $this->getAlias($image);

        // Custom
        $image->name = substr(yii\helpers\Inflector::slug($pictureFileName), 0, -3);
        // Get the highest position
        // @todo Create function
        $owner = $this->owner;
        $query = (new yii\db\Query())->select('MAX(`position`)')->from(Image::tableName())->where(['modelName' => yii\helpers\StringHelper::basename($owner::className())]);
        $command = $query->createCommand();
        $image->position = $command->queryOne(\PDO::FETCH_COLUMN)+1;

        if(!$image->save()){
            return false;
        }

        if (count($image->getErrors()) > 0) {

            $ar = array_shift($image->getErrors());

            unlink($newAbsolutePath);
            throw new \Exception(array_shift($ar));
        }
        $img = $this->owner->getImage();

        //If main image not exists
        if(
            is_object($img) && get_class($img)=='rico\yii2images\models\PlaceHolder'
            or
            $img == null
            or
            $isMain
        ){
            $this->setMainImage($image);
        }


        return $image;
    }

    /** Make string part of image's url
     * @return string
     * @throws \Exception
     */
    private function getAliasString()
    {
        if ($this->createAliasMethod) {
            $string = $this->owner->{$this->createAliasMethod}();
            if (!is_string($string)) {
                throw new \Exception(Yii::t('app', 'Invalid image alias'));
            } else {
                return $string;
            }

        } else {
            return substr(md5(microtime()), 0, 10);
        }
    }

    /**
     *
     * Обновить алиасы для картинок
     * Зачистить кэш
     */
    private function getAlias()
    {
        $imagesCount = count($this->owner->getImages());

        return $this->getImage()->name . '-' . intval($imagesCount + 1);
    }

    /**
     * Returns model images
     * First image alwats must be main image
     * @return array|yii\db\ActiveRecord[]
     */
    public function getImages()
    {
        $finder = $this->getImagesFinder();

        $imageQuery = Image::find()
            ->where($finder);
        $imageQuery->orderBy(['position' => SORT_DESC]);
        $imageRecords = $imageQuery->all();

        if(!$imageRecords){
            return [$this->getModule()->getPlaceHolder()];
        }
        return $imageRecords;
    }

    /**
     * Remove all model images
     */
    public function removeImages()
    {
        $images = $this->owner->getImages();
        if (count($images) < 1) {
            return true;
        } else {
            foreach ($images as $image) {
                $this->owner->removeImage($image);
            }
        }
    }

    /**
     * Returns main model image
     * @param   boolean $fallbackToPlaceholder      A flag to determine if a 
     *                                              placeholder has to be used
     *                                              when no image is found
     * @param   mixed   $placeHolderPath            The alternative placeholder path
     * @return  array|null|ActiveRecord
     */
    public function getImage($fallbackToPlaceholder = true, $placeHolderPath = null)
    {

        $finder = $this->getImagesFinder(['isMain' => 1]);

        $imageQuery = Image::find()->where($finder);

        $img = $imageQuery->one();

        // No image model + fallback to placeholder or
        // image model but image does not exist + fallback to placeholder
        if ((!$img && $fallbackToPlaceholder) ||
            ($img !== null && !file_exists($img->getBaseUrl()) && $fallbackToPlaceholder)) {
            
            // Custom placeholder
            if ($placeHolderPath) {
                $placeHolder = new Image([
                    'filePath' => basename(Yii::getAlias($placeHolderPath)),
                    'urlAlias' => basename($placeHolderPath)
                ]);
                
                return $placeHolder;
            // Default placeholder
            } else {
                return $this->getModule()->getPlaceHolder();    
            }
        }

        return $img;
    }

    private function getImagesFinder($additionWhere = false)
    {
        $base = [
            'itemId' => $this->owner->id,
            'modelName' => $this->getModule()->getShortClass($this->owner)
        ];

        if ($additionWhere) {
            $base = \yii\helpers\BaseArrayHelper::merge($base, $additionWhere);
        }

        return $base;
    }

    /**
     * Returns model images
     * First image alwats must be main image
     * @return array|yii\db\ActiveRecord[]
     */
    public function clearCache()
    {
        $finder = $this->getImagesFinder();

        $imageQuery = Image::find()
            ->where($finder);
        $imageQuery->orderBy(['isMain' => SORT_DESC, 'id' => SORT_ASC]);

        $imageRecords = $imageQuery->all();
        if(!$imageRecords){
            return [$this->getModule()->getPlaceHolder()];
        }
        return $imageRecords;
    }

    /**
     *
     * removes concrete model's image
     * @param Image $img
     * @throws \Exception
     */
    public function removeImage(\rico\yii2images\models\Image $img)
    {
        if (!$img->isNewRecord) {
            $imgInfoweb = Image::findOne(['id' => $img->id]);
            $imgInfoweb->clearCache();

            $storePath = $this->getModule()->getStorePath();

            $fileToRemove = $storePath . DIRECTORY_SEPARATOR . $img->filePath;
            if (preg_match('@\.@', $fileToRemove) and is_file($fileToRemove)) {
                unlink($fileToRemove);
            }
            $img->delete();
        }
    }

    /**
     * Upload and attach images
     *
     * @param $model
     */
    public function uploadImage() {

        // Upload image
        $form = new ImageUploadForm();
        $images = UploadedFile::getInstances($form, 'image');

        $model = $this->owner;

        // Remove old images if a new one is uploaded
        if ($images) {
            $model->removeImages();

            foreach ($images as $k => $image) {

                $_model = new ImageUploadForm();
                $_model->image = $image;

                if ($_model->validate()) {
                    $path = \Yii::getAlias('@uploadsBasePath') . "/img/{$_model->image->baseName}.{$_model->image->extension}";

                    $_model->image->saveAs($path);

                    // Attach image to model
                    $model->attachImage($path);

                } else {
                    foreach ($_model->getErrors('image') as $error) {
                        $model->addError('image', $error);
                    }
                }
            }

            if ($model->hasErrors('image')){
                $model->addError(
                    'image',
                    count($model->getErrors('image')) . Yii::t('app', 'of') . count($images) . ' ' . Yii::t('app', 'images not uploaded')
                );
            } else {
                Yii::$app->session->setFlash(StringHelper::basename($this->owner->className()), Yii::t('app', '{n, plural, =1{Image} other{# images}} successfully uploaded', ['n' => count($images)]));
            }
        }
    }

}