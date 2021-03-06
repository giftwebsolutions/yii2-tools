<?php
/**
 * FileBehavior class file.
 * @copyright (c) 2015, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace bariew\yii2Tools\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
/**
 * This is for saving model file.
 * It takes uploaded file from owner $fileField attribute
 * moves it to custom path and updates owner $fileField in db which is just alias
 * for real file name.
 * Real file name and path is constant for owner like /web/files/{ownerClassName}/{owner_id}
 * You can define your own method for file path.
 * You can also get file with this->showFile() or this->sendFile() method.
 *
 * Usage:
 * Define this behavior in your ActiveRecord instance class.
    public function behaviors() 
    {
        return [
            'fileBehavior' => [
                'class' => \bariew\yii2Tools\FileBehavior::className(),
                'fileField' => 'image',
                'imageSettings' => [
                    'thumb1' => ['method' => 'thumbnail', 'width' => 50, 'height' => 50],
                    'thumb2' => ['method' => 'thumbnail', 'width' => 100, 'height' => 100],
                    'thumb3' => ['method' => 'thumbnail', 'width' => 200, 'height' => 200],
                ]
            ]
        ];
    }
 * For multiple upload just follow common rules (set rules maxFiles, set input name[] and set input multiple=>true):
 * @see https://github.com/yiisoft/yii2/blob/master/docs/guide/input-file-upload.md
 *
 *
 * @author Pavel Bariev <bariew@yandex.ru>
 * @property ActiveRecord $owner
 */
class FileBehavior extends Behavior
{
    /**
     * @var string base path for all files.
     */
    public $storage = '@app/web/files';

    /**
     * @var string owner required uploaded file field name.
     */
    public $fileField;

    /**
     * @var string optional owner filePath naming method. By default we use inner $this->getFilePath() method.
     */
    public $pathCallback;

    /**
     * @var array settings for saving image thumbs.
     */
    public $imageSettings = [];
    
    public $files = [];
    
    protected $fileName = '';
    
    protected $fileNumber = 0;
    
    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_AFTER_VALIDATE => 'afterValidate',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete'
        ];
    }

    /**
     * Attaches uploaded file to owner.
     */
    public function beforeValidate()
    {
        $this->fileName = $this->owner->getAttribute($this->fileField);
        if (!is_string($this->fileName)) {
            $this->fileName = @$this->owner->oldAttributes[$this->fileField];
        }
        if (
            (!$files = UploadedFile::getInstance($this->owner, $this->fileField))
            && (!$files = UploadedFile::getInstances($this->owner, $this->fileField))     
        ) {
            return true;
        }
        return $this->owner->setAttribute($this->fileField, $files);
    }

    /**
     * @return bool
     */
    public function afterValidate()
    {
        if (!$files = $this->owner->getAttribute($this->fileField)) {
            return true;
        }
        if (!is_array($files)) {
            $files = [$files];
        }
        $this->owner->setAttribute($this->fileField, $this->fileName);
        if (current($files) instanceof UploadedFile) {
            $this->files = $files;
        }
        return true;
    }

    /**
     * Saves attached file and sets db filename field, makes thumbnails.
     */
    public function afterSave()
    {
        if (!$this->files) {
            return true;
        }
        $oldFileCount = $this->getFileCount();
        /**
         * @var UploadedFile $file
         */
        foreach ($this->files as $key => $file) {
            $this->fileNumber = $oldFileCount + $key + 1;
            $this->fileName = $this->fileNumber . '_' . $file->name;
            if ($this->fileNumber == 1) {
                $this->owner->updateAttributes([$this->fileField => $this->fileName]);
            }
            $path = $this->getFilePath(null, $this->fileName);
            $this->createFilePath($path);
            $file->saveAs($path);
            foreach ($this->imageSettings as $name => $options) {
                $this->processImage($name, $options);
            }    
        }
    }

    /**
     * Removes owner files.
     */
    public function afterDelete()
    {
        foreach ($this->getAllFields() as $field) {
            $path = $this->getFilePath($field, '');
            if (file_exists($path) && is_dir($path)) {
                FileHelper::removeDirectory($path);
            }
        }
    }

    /**
     *
     * @return array
     */
    protected function getAllFields()
    {
        return array_merge([null], array_keys($this->imageSettings));
    }

    /**
     * Gets file full path.
     * @param null $field
     * @param null $name
     * @return bool|mixed|string
     */
    public function getFilePath($field = null, $name = null)
    {
        if ($this->pathCallback) {
            return call_user_func([$this->owner, $this->pathCallback]);
        }
        if (($name === null) && (!$name = $this->getFirstFileName())) {
            return false;
        }
        $storage = is_callable($this->storage)
            ? call_user_func($this->storage) : $this->storage;
        $field = $field ? '_' . preg_replace('/[^\-\w]+/', '', $field) : '';
        return \Yii::getAlias(
            $storage 
            . '/' . $this->fileField . $field
            . '/' . preg_replace('/[^\.\-\w]+/', '', $name)
        );
    }

    /**
     * Gets url to file
     * @param null $field
     * @param null $name
     * @return mixed
     */
    public function getFileLink($field = null, $name = null)
    {
        $root = realpath(\Yii::getAlias('@webroot'));
        return str_replace($root, '', $this->getFilePath($field, $name));
    }

    /**
     * Name of first file
     * @return mixed
     */
    public function getFirstFileName()
    {
        return $this->owner->getAttribute($this->fileField);
    }

    /**
     * Count of files
     * @return int
     */
    public function getFileCount()
    {
        if (!$files = $this->getFileList()) {
            return 0;
        }
        $lastName = end($files);
        return preg_match('/^(\d+)_.*$/', $lastName, $matches)
            ? $matches[1] : count($files);
    }

    /**
     * All files list for field
     * @param null $field
     * @return array
     */
    public function getFileList($field = null)
    {
        $dir = $this->getFilePath($field, '');
        if (!file_exists($dir) || !is_dir($dir)) {
            return [];
        }
        return array_diff(scandir($dir), ['.', '..']);
    }

    /**
     * Url to a file from list
     * @param null $field
     * @param int $position
     * @return mixed|null
     */
    public function getFilePositionLink($field = null, $position = 0)
    {
        $list = array_values($this->getFileList($field));
        return isset($list[$position])
            ? $this->getFileLink($field, $list[$position])
            : null;
    }

    /**
     * Shows file to the browser.
     * @throws NotFoundHttpException
     */
    public function showFile($field = null, $name = null)
    {
        $file = $this->getFilePath($field, $name);
        if (!file_exists($file) || !is_file($file)) {
            throw new NotFoundHttpException;
        }
        header('Content-Type: '. FileHelper::getMimeType($file), true);
        header('Content-Length: ' . filesize($file));
        readfile($file);
    }

    /**
     * Sends file to user download.
     * @param null $field
     * @param null $name
     * @return bool
     * @throws NotFoundHttpException
     */
    public function sendFile($field = null, $name = null)
    {
        if (!$name && (!$name = $this->getFirstFileName())) {
            return false;
        }
        $file = $this->getFilePath($field, $name);
        if (!$name || !file_exists($file)) {
            throw new NotFoundHttpException;
        }
        \Yii::$app->response->sendFile($file, $name);
    }

    /**
     * Deletes file and all thumbnails by name
     * @param null $name
     * @return bool
     */
    public function deleteFile($name = null)
    {
        foreach ($this->getAllFields() as $field) {
            $path = $this->getFilePath($field, $name);
            if (!$path || !is_file($path) || !file_exists($path)) {
                continue;
            }
            unlink($path);
        }
        if ($name == $this->owner->getAttribute($this->fileField)
           && ($files = $this->getFileList())     
        ) {
            $this->owner->updateAttributes([$this->fileField => reset($files)]);
        }
        return true;
    }

    /**
     * Renames file
     * @param $name
     * @param $newName
     * @return bool
     * @throws \Exception
     */
    public function renameFile($name, $newName)
    {
        foreach ($this->getAllFields() as $field) {
            $path = $this->getFilePath($field, $name);
            $newPath = $this->getFilePath($field, $newName);
            if (file_exists($newPath)) {
                throw new \Exception("File {$newName} already exists");
            }
            rename($path, $newPath);
        }
        if ($name == $this->owner->getAttribute($this->fileField)
            && ($files = $this->getFileList())
        ) {
            $this->owner->updateAttributes([$this->fileField => $newName]);
        }
        return true;
    }

    /**
     * Generates path recursively.
     * @param string $path path to create.
     * @return bool
     */
    private function createFilePath($path)
    {
        $dir = dirname($path);
        return file_exists($dir) || FileHelper::createDirectory($dir, 0775, true);
    }
    
    /**
     * Creates image copies processed with options
     * @param string $field thumbnail name.
     * @param array $options processing options.
     */
    private function processImage($field, $options)
    {
        $originalPath = $this->getFilePath(null, $this->fileName);
        $resultPath = $this->getFilePath($field, $this->fileName);
        $this->createFilePath($resultPath);
        switch ($options['method']) {
            case 'thumbnail' :
                \yii\imagine\Image::thumbnail(
                    $originalPath, $options['width'], $options['height']
                )->save($resultPath, [
                    'format' => pathinfo($this->fileName, \PATHINFO_EXTENSION)
                ]);
                break;
        }
    }

    /**
     * Gets links for all model files.
     * @param null $field
     * @return array
     */
    public function linkList($field = null)
    {
        $result = [];
        foreach ($this->getFileList() as $path) {
            $name = basename($path);
            $result[$name] = $this->getFileLink($field, basename($path));
        }
        return $result;
    }
}