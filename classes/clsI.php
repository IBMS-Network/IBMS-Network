<?php

namespace classes;

use classes\core\clsDB;
use entities\Image;

if (!defined('IMAGE_TH_PATTERN'))
    define ('IMAGE_TH_PATTERN', '%s/%s/%s.%s');

/**
 *
 * Image storage system.
 * Main idea:
 *
 * 1) РҐСЂР°РЅРµРЅРёРµ РєР°СЂС‚РёРЅРѕРє Р±РµР· РїСЂРёРІСЏР·РєРё Рє ID, Р° РѕР±С‰РёРј СЃРїРёСЃРєРѕРј
 * 2) РЎРѕР·РґР°РЅРёРµ РёРјРµРЅРё РєР°СЂС‚РёРЅРєРё РєР°РєРёРј-РЅРёР±СѓРґСЊ РѕР±СЂР°Р·РѕРј (РїСЂРµРґР»Р°РіР°СЋ MD + CRC(timestamp))
 * 3) Р�РјСЏ РєР°СЂС‚РёРЅРєРё СЃСЂР°Р·Сѓ С…СЂР°РЅРёС‚ СЂР°Р·РјРµСЂРЅРѕСЃС‚СЊ.РўРёРїР° x-y-afd46bc...
 * 4) РљР°РєР°СЏ-С‚Рѕ С‡Р°СЃС‚СЊ РёРјРµРЅРё СЏРІР»СЏРµС‚СЃСЏ РµС‰Рµ Рё РїСѓС‚РµРј РґР»СЏ СѓРјРµРЅСЊС€РµРЅРёСЏ РєРѕР»-РІР° РєР°СЂС‚РёРЅРѕРє РІ РїР°РїРєРµ
 * 5) Р РµСЃР°Р№Р·С‹ С…СЂР°РЅРёС‚СЊ С‚Р°Рє Р¶Рµ, РєР°Рє Рё СЃРµР№С‡Р°СЃ -service_id/size_id/...
 *
 * Р РµР°Р»РёР·РѕРІР°С‚СЊ РєР»Р°СЃСЃ СЂР°Р±РѕС‚С‹ СЃ РєР°СЂС‚РёРЅРєР°РјРё:
 * - СЃРѕР·РґР°С‚СЊ РёРјСЏ
 * - РїРѕР»РѕР¶РёС‚СЊ РєР°СЂС‚РёРЅРєСѓ
 * - СЃРґРµР»Р°С‚СЊ СЂРµСЃР°Р№Р·
 * - СѓРґР°Р»РёС‚СЊ (СЃРѕ РІСЃРµРјРё СЂРµСЃР°Р№Р·Р°РјРё)
 * - Р·Р°РјРµРЅРёС‚СЊ РєР°СЂС‚РёРЅРєСѓ (СѓРґР°Р»РёС‚СЊ РІСЃРµ СЃС‚Р°СЂС‹Рµ СЂРµСЃР°Р№Р·С‹)
 *
 *
 */
class clsI{

    /**
     * Cache
     *
     * @var array
     */
    private $gITCache = array();

    /**
     * Secure hash
     *
     * @var string
     */
    private $secure = '@wdwfghd0=0w\$_4gj*0-=\12kdfdfb7xcvxcv789!7455-*A*+*';

    /**
     * Exec switcher
     *
     * @var bool
     */
    public $doNotExec = false;

    /**
     * Max width in px
     *
     * @var string
     */
    private $max_width = '5000';

    /**
     * Max height in px
     *
     * @var string
     */
    private $max_height = '5000';

    /**
     * Рњax weight in bytes
     *
     * @var int
     */
    private $max_weight = 10485760;

    /**
     * Рњin width in px
     *
     * @var string
     */
    private $min_width = '542';

    /**
     * Рњin height in px
     *
     * @var string
     */
    private $min_height = '396';

    /**
     * set allowed extention for images $allow_ext
     *
     *    1 = IMAGETYPE_GIF,
     *    2 = IMAGETYPE_JPG,
     *    3 = IMAGETYPE_PNG,
     *    4 = IMAGETYPE_SWF,
     *    5 = IMAGETYPE_PSD,
     *    6 = IMAGETYPE_BMP,
     *    7 = IMAGETYPE_TIFF_II (intel byte order),
     *    8 = IMAGETYPE_TIFF_MM (motorola byte order),
     *    9 = IMAGETYPE_JPC,
     *    10 = IMAGETYPE_JP2,
     *    11 = IMAGETYPE_JPX.
     *
     * @var array
     */
    private $allow_types = array( 1, 2, 3 );

    /**
     * Image dir
     *
     * @var string
     */
    private $image_dir = SYS_IMAGE_PATH;

    /**
     * Thumbnails dir
     *
     * @var string
     */
    private $th_dir = SYS_IMAGE_TH_PATH;

    /**
     * Thumbnail whitemargin
     *
     * @var bool
     */
    private $th_whitemargin = false;

    /**
     * Thumbnail jpeg quality
     *
     * @var int
     */
    private $th_jpeg_quality = 85;

    /**
     * Num symbols in sub folder name
     *
     * @var int
     */
    private $folder_length = 4;

    /**
     * Image cache lifetime
     *
     * @var int
     */
    private $images_cache_lifetime = 2592000;

    /**
     * Use GD library instead of ImageMagic
     *
     * @var bool
     */
    private $useResizeImage_gd = true;

    /**
     * Path to imagemagick convert utility
     *
     * @var string
     */
    private $path_to_imagemagick_convert = 'convert';

    /**
     * Magic setter
     *
     * @param $name
     * @param $value
     */
    function __set($name, $value){
        $this->$name = $value;
    }

    /**
     * Magic getter
     *
     * @param $name
     * @return mixed
     */
    function __get($name){
        return $this->$name;
    }

    /**
     * Get image dir
     *
     * @return string
     */
    function getImageDir(){
        return $this->image_dir;
    }

    /**
     * Constructor
     */
    function __construct(){
//        global $error_text;

        require_once(CONFIG_PATH."th-sizes.php");
        $this->db = clsDB::getInstance();

    }
    
    /**
    * Instance
    *
    * @return clsI
    */
    static function getInstance(){
        static $instance = false;
        if( empty( $instance ) ){
            $instance = new self( true );
        }
        return $instance;
    }

    /**
     * add image to db layer
     *
     * @param string $name
     * @param string $orig_name
     * @param $s  = getimagesize()
     * @param int $file_size
     * @param int $th
     * @return mixed|string
     */

    function addImageToDB( $name, $orig_name, $s, $file_size, $th = 0 ){
        list( $width, $height, $type ) = $s;
        //$full_key РјРѕР¶РЅРѕ РёСЃРїРѕР»СЊР·РѕРІС‚СЊ РґР»СЏ РІС‹Р±РѕСЂРєРё РїРѕ РѕРґРЅРѕРјСѓ РїРѕР»СЋ, РµСЃР»Рё СЌС‚Рѕ РєСЂРёС‚РёС‡РЅРѕ РїРѕ РІСЂРµРјРµРЅРё (РґР»СЏ РґРѕРєСѓРјРµРЅС‚РѕСЂРёРµРЅС‚РёСЂРѕРІР°РЅРЅС‹С… СЃСѓР±Рґ)
        $full_key = md5( $name . $type . $width . $height . (int)$th );
        $image = new Image();
        $image->setName($name);
        $image->setType($type);
        $image->setWidth($width);
        $image->setHeight($height);
        $image->setTh($th);
        $image->setWeight($file_size);
        $image->setFullKey($full_key);
        $image->setOrigName($orig_name);
        $image->setTs(new \DateTime());
        
        $this->db->persist($image);
        $this->db->flush();

//        if(!$r) {
//            return 'db error';
//        }
//        else {
//            $result = $this->db->getRepository('entities\Image')->findOneBy(array('full_key', $full_key));
            return $image->getId();
//        }
    }

    /**
     * Add thumbnail to db layer
     *
     * @param $dst
     * @param $name
     * @param $size_code
     * @return mixed|string
     */
    function addThToDB( $dst, $name, $size_code ){
        $image_size = getimagesize( $dst );//array
        //$full_key РјРѕР¶РЅРѕ РёСЃРїРѕР»СЊР·РѕРІС‚СЊ РґР»СЏ РІС‹Р±РѕСЂРєРё РїРѕ РѕРґРЅРѕРјСѓ РїРѕР»СЋ, РµСЃР»Рё СЌС‚Рѕ РєСЂРёС‚РёС‡РЅРѕ РїРѕ РІСЂРµРјРµРЅРё (РґР»СЏ РґРѕРєСѓРјРµРЅС‚РѕСЂРёРµРЅС‚РёСЂРѕРІР°РЅРЅС‹С… СЃСѓР±Рґ)
        $full_key = md5( $name . $image_size[2] . $image_size[0] . $image_size[1] . (int)$size_code );

        if(!$result = $this->db->getRepository('entities\Image')->findOneBy(array('full_key', $full_key))){
            $image = new Image();
            $image->setName($name);
            $image->setType($image_size[2]);
            $image->setWidth($image_size[0]);
            $image->setHeight($image_size[1]);
            $image->setTh($size_code);
            $image->setWeight(filesize( $dst ));
            $image->setFullKey($full_key);
            $image->setOrigName("");
            $image->setTs(new \DateTime());
            
            $this->db->persist($image);
            $this->db->flush();
        }
        return $result ? $result->getId() : $image->getId();

    }

    /**
     * Check image
     *
     * @param unknown_type $file = $_FILES['userfile']
     * @param unknown_type $is_validate_full
     */
    function checkImage( $file, $is_validate_full = false){
        if( $file["size"] > $this->max_weight ) {
            return 1;
        }
        if(!$file['tmp_name']){
            return 7;
        }
        $is = getimagesize( $file['tmp_name'] );

        $width = $is[0];
        $height = $is[1];
        $type = $is[2];

        if( $width > $this->max_width ) {
            return 2;
        }
        if( $height > $this->max_height ) {
            return 3;
        }
        if( !in_array( $type, $this->allow_types ) ) {
            return 4;
        }
        if( $is_validate_full && $width < $this->min_width  ) {
            return 5;
        }
        if( $is_validate_full && $height < $this->min_height ) {
            return 6;
        }

        return $is;
    }

    /**
     * Create name
     * @param $tmp_name
     * @param $s
     * @return string
     */
    function createName( $tmp_name, $s ){
        return $s[0] . '@' . $s[1] . '@' . md5( $tmp_name . $s[2]/*type*/ . microtime( true ) );
    }

    /**
     * Re-put existing file to place
     *
     * @param $file
     * @param bool $unlink
     * @return array|bool
     */
    function reputToPlace( $file, $unlink = false){
        $weight = filesize($file);
        if( $weight > $this->max_weight ) {
            return false;
        }

        $is = getimagesize( $file );
        if( !$is ) {
            // die('bad file');
            return false;
        }

        $width = $is[0];
        $height = $is[1];
        $type = $is[2];

        if( $width > $this->max_width ) {
            return false;
        }

        if( $height > $this->max_height ) return false;
        if( !in_array( $type, $this->allow_types ) ) return false;

        $name = $this->createName( $file, $is );
        $upload_file = $this->makePath( $is, $name );

        if (copy($file,$upload_file)) {
            if($unlink){
                unlink($file);
            }
        } else {
            // die('copy file error ['.$file.' to '.$upload_file.']');
            return false;
        }
        //$name = $name . '.'.$this->image_type_to_extension( $is[2] );
        $id = $this->addImageToDB( $name, $file /*orig name*/, $is /* = getimagesize()*/, $weight, $th = 0 );

        if( is_file( $file ) && $unlink) unlink( $file );

        //$path = (int)$is[0] . '/' . (int)$is[1] . '/';
        return array( $id, $name);
    }

    /**
     * Only for http file upload
     *
     * @param $file $_FILES['userfile']
     * @return array|bool
     */
    function putToPlace( $file /* =  */ ){
        $file_size = $file['size'];//weight
        $orig_name = $file['name'];
        $image_size = $this->checkImage( $file );//array

        if( !is_array($image_size) ){
            return false;
        }

        $name = $this->createName( $file['tmp_name'], $image_size );
        $upload_file = $this->makePath( $image_size, $name );

        $move_result = @move_uploaded_file( $file['tmp_name'], $upload_file );
        if ( !$move_result ){
            $this->_returnError('photo_move_error');
            return false;
        }else{
            $id = $this->addImageToDB( $name, $orig_name, $image_size /* = getimagesize()*/, $file_size, $th = 0 );
        }

        if( is_file( $file['tmp_name'] ) ) unlink( $file['tmp_name'] );

        return array( $id, $name );
    }

    /**
     * Get image sub-folder name by image name
     *
     * @param $name
     * @return string
     */
    function subfolder( $name ){
        $sf = explode( '@', $name );
        $sf = empty($sf[2]) ? '' : $sf[2];
        return substr( $sf, 0, $this->folder_length ) . '/' . substr( $sf, $this->folder_length, $this->folder_length );
    }

    /*
    * Get path to image by image ID
    *
    * @param integer $id
    *   image ID from DB
    * @param integer $size_code
    *   index of assoc array $GLOBALS["sizes"] from th-sizes.php. If it's 0 then
    *   return nominal size
    */
    function resize( $id, $size_code = 0 ){
        if(empty($id)) {
            // * return empty string if id is empty
            return "";
        }
        $image = $this->db->getRepository('entities\Image')->findOneBy(array('id' => (int)$id));

        if(empty($image)) {
            // * if we dont find eny records related with incoming id we return empty string
            return "";
        }
        $ext = $this->image_type_to_extension( (int)$image->getType() );
        //$path = $this->th_dir . $size_code . '/' . $this->subfolder( $image['name'] ) . '/' . $image['name'] . '-' . $this->hashme($image['name']) . '.' . $ext;
        $path = (!empty($size_code) ? ($size_code. '/') :  "0/") . $this->subfolder( $image->getName() ) . '/' . $image->getName() . '-' . $this->hashme($image->getName()) . '.' . $ext;
        return $path;
    }

    /*
    * get path to image by image Name
    *
    * @param integer $name
    *   image name from DB
    * @param integer $size_code
    *   index of assoc array $GLOBALS["sizes"] from th-sizes.php. If it's 0 then
    *   return nominal size
    */
    function resizeByName( $name, $size_code = 0 ){
        if(empty($name)) {
            // * return empty string if name is empty
            return "";
        }
        $image = $this->db->getRepository('entities\Image')->findOneBy(array('name' => $name));
        if(empty($image)) {
            // * if we dont find eny records related with incoming id we return empty string
            return "";
        }
        $ext = $this->image_type_to_extension( (int)$image->getType() );
        //$path = $this->th_dir . $size_code . '/' . $this->subfolder( $image['name'] ) . '/' . $image['name'] . '-' . $this->hashme($image['name']) . '.' . $ext;
        $path = (!empty($size_code) ? ($size_code. '/') :  "") . $this->subfolder( $image->getName() ) . '/' . $image->getName() . '-' . $this->hashme($image->getName()) . '.' . $ext;
        return $path;
    }

    /**
     * Delete image by name
     *
     * @param $name
     * @return bool
     */
    function clearByName( $name ){

        $all = $this->db->getRepository('entities\Image')->findBy( array('name' => $name) );
        foreach( $all as $c ){
            if( $c->getTh() ) {
                $path = $this->imagePattern( $c->getTh(), $c->getName(), str_replace( '.', '', $this->image_type_to_extension( $c->getType() ) ) );
            }
            else {
                $path = $this->image_dir . $c->getWidth() . '/' . $c->getHeight() . '/' . $c->getName() . '.' . $this->image_type_to_extension( $c->getType() );
            }
            if(is_file( $path )){
                $my_del = unlink( $path );
            }
            
            $serv = $this->db->getRepository('entities\ServiceImage')->findBy("image_id", (int)$c->getId());
            $this->db->remove($serv);
        }
        
        $this->db->remove($all);
        $this->db->flush();
        
        return true;
    }

    /**
     * Delete image by id
     *
     * @param $id
     * @return bool
     */
    function clearByID( $id ){
        $image  = $this->db->getRepository('entities\Image')->findOneBy( array('id', $id) );
        return $this->clearByName( $image->getName() );
    }

    /**
     * Replace image by name
     *
     * @param $old_name
     * @param $file
     */
    function replaceByName( $old_name, $file /* = $_FILES['userfile'] */ ){
        $this->putToPlace( $file );
        $image  = $this->db->getRepository('entities\Image')->findOneBy( array('name', $old_name) );
        $this->db->remave($image);
        $this->db->flush();
    }

    /**
     * Replace image by id
     *
     * @param $old_id
     * @param $file
     * @return array|bool
     */
    function replaceByID( $old_id, $file /* = $_FILES['userfile'] */ ){
        $result = $this->putToPlace( $file );
        list($fid, $fpath) = $result;
        $this->clearByID( $old_id );
        return $result;
    }

    /**
     * Open image file $src.
     * Set $type and array $s (result of  getimagesize($src)), passed by reference.
     *
     * @param string $src - image file path
     * @param string $type - [returns] image type constant
     * @return resource - GD image
     */
    function getImageType($src, &$s) {
        if (isset($this->gITCache[$src])) {
            $s = $this->gITCache[$src][1];
            return $this->gITCache[$src][0];
        }
        if (!file_exists($src)) return false;
        $s = @getimagesize($src);
        if (!$s) return false;
        if (!function_exists('exif_imagetype')) {
            if (false !== strpos($s['mime'],"image/jpeg")) $type = IMAGETYPE_JPEG;
            if (false !== strpos($s['mime'],"image/gif")) $type = IMAGETYPE_GIF;
            if (false !== strpos($s['mime'],"image/png")) $type = IMAGETYPE_PNG;
        }
        else {
            $type = exif_imagetype($src);
        }
        $this->gITCache[$src] = array($type, $s);
        return $type;
    }

    /**
     * Get image pattern
     * @param $size
     * @param $name
     * @param $ext
     * @param string $hash
     * @return string
     */
    function imagePattern( $size, $name, $ext, $hash='' ) {
        return sprintf( $this->th_dir . IMAGE_TH_PATTERN, $size, $this->subfolder( $name ) , $name . (strlen($hash)?('-'.$hash):''), $ext);
    }

    /**
     * Validate image path
     *
     * @param $path
     * @return array|bool
     */
    function imageValidatePath($path) {
        if ( !preg_match("~([0-9]+)\/([a-zA-Z0-9]+\/[a-zA-Z0-9]+)\/([@a-zA-Z0-9]+)-([a-zA-Z0-9]+)\.([a-z]+)$~", $path, $matches) ) {
            return false;
        }
        list( $path, $size_code, $subfolder, $name, $hash, $ext ) = $matches;

        $sizes = $GLOBALS['isSizes'];

        if(!isset($sizes[$size_code])) {
            return false;
        }

        $h=$this->imagePattern( $size_code, $name, $ext );

        $h=substr(base64_encode(substr(sha1($name.$this->secure),30)),0,-2);

        return (0===strcmp($h,$hash) ? array($size_code,$name,$ext,$hash) : false);
    }

    /**
     * Get hash
     *
     * @param $s
     * @return string
     */
    function hashme(&$s){
        return substr(base64_encode(substr(sha1($s.$this->secure),30)),0,-2);
    }

    /**
     * Change size of file $src to width $new_width Рё height $new_height,
     * provides a tough match the size of ($cut=true)
     * save result to file $dst
     * in accordance with the file type $src, Unless otherwise specified type $force_type
     *
     * @param string $src - source image file path
     * @param string $dst - destination image file path
     * @param int $new_width - width
     * @param int $new_height - height
     * @param boolean $cut - force use $new_width, $new_height even $src sizes is smaller
     * @param int $force_type - image type constant
     * @param boolean $whitemargin - Big picture is reduced in order to fully get into new dimensions. Add fields to image.
     * @return void | false on error
     */
    function resizeImage($src, $dst = '', $new_width = 150, $new_height = 150, $cut = false, $whitemargin=null, $params=array())
    {
        if($new_height == 0){
            $new_height = 150;
        }
        if (is_null($whitemargin)) $whitemargin = $this->th_whitemargin;
        if (empty($dst)) return false;
            //return $this->resizeImage_imagick($src, $dst, $new_width, $new_height, $cut, $whitemargin, $params);
        if ($this->useResizeImage_gd) {
            return $this->resizeImage_gd($src, $dst, $new_width, $new_height, $cut, $whitemargin, $params);
        } else {
            return $this->resizeImage_imagick($src, $dst, $new_width, $new_height, $cut, $whitemargin, $params);
        }
    }

    /**
     * Change size of file $src to width $new_width Рё height $new_height,
     * provides a tough match the size of ($cut=true)
     * save result to file $dst
     * in accordance with the file type $src, Unless otherwise specified type $force_type
     *
     * @param string $src - source image file path
     * @param string $dst - destination image file path
     * @param int $new_width - width
     * @param int $new_height - height
     * @param boolean $cut - force use $new_width, $new_height even $src sizes is smaller
     * @param int $force_type - image type constant
     * @param boolean $whitemargin - Big picture is reduced in order to fully get into new dimensions. Add fields to image.
     * @return void | false on error
     */
    function resizeImage_imagick($src, $dst, $new_width, $new_height, $cut, $whitemargin, $params=array())
    {
        $type = $this->getImageType($src, $size);
        if (!$type) return false;
        $filename_without_ext = substr($dst, 0, strrpos($dst, '.')).'.';
        $ext = $this->image_type_to_extension($type);
        $dst = $filename_without_ext . $ext;
        $is_jpg = (IMAGETYPE_JPEG == $type);
        $is_png = (IMAGETYPE_PNG  == $type);
        $is_gif = (IMAGETYPE_GIF  == $type);

        if ($cut) {
            $_new_width = $new_width; $_new_height = $new_height;	 // СЂР°Р·РјРµСЂС‹ РІС‹С…РѕРґРЅРѕРіРѕ С„Р°Р№Р»Р° ($cut=true)
            $this->calcImageSize($size, $new_width, $new_height, $whitemargin);
        }
        else {
            $this->calcImageSize($size, $new_width, $new_height, 1);
            $_new_width = $new_width; $_new_height = $new_height;
        }

        //$com = '/usr/local/bin/convert -fill "rgba(255,255,255,1)" '.$src;
        $com = $this->path_to_imagemagick_convert . ' -fill "rgba(255,255,255,0)" '.$src;
        if (!$is_gif) $com .= " -type TrueColorMatte";
        // here is given crop (calculated sizes to which the image is changed)
        $com .= " -resize ".$new_width."x".$new_height;
        $com .= ' -size '.$_new_width."x".$_new_height;
        $com .= ($is_jpg) ? ' xc:#fff' : ' xc:"rgba(255,255,255,0)"'; // background color (white = 1 | transparent = 0) "rgba(255,255,255,1)"
        $geo = (isset($params['gravity'])) ? $params['gravity'] : 'center';
        $com .= ' +swap -gravity '.$geo.' -composite'; // set center
        if (isset($params['rotate'])) $com .= ' -rotate '.$params['rotate'];
        if (!$is_gif) $com .= ' -quality '.$this->th_jpeg_quality." -strip";
        $com .= " ".$dst;

        if ($this->doNotExec) {
            return $com;
        }
        else {
            $output = '';
            exec($com, $output);
        }
        if (!is_file($dst)) exit($com);
        return array($ext, $size['mime']);
    }
    /**
     * Change size of file $src to width $new_width Рё height $new_height,
     * provides a tough match the size of ($cut=true)
     * save result to file $dst
     * in accordance with the file type $src, Unless otherwise specified type $force_type
     *
     * @param string $src - source image file path
     * @param string $dst - destination image file path
     * @param int $new_width - width
     * @param int $new_height - height
     * @param boolean $cut - force use $new_width, $new_height even $src sizes is smaller
     * @param int $force_type - image type constant
     * @param boolean $whitemargin - Big picture is reduced in order to fully get into new dimensions. Add fields to image.
     * @return void | false on error
     */
    function resizeImage_gd($src, $dst, $new_width, $new_height, $cut, $whitemargin, $params=array())
    {
        $type = $this->getImageType($src, $size);
        if (!$type) return false;
        $filename_without_ext = substr($dst, 0, strrpos($dst, '.')).'.';
        $ext = $this->image_type_to_extension($type);
        $dst = $filename_without_ext . $ext;
        $is_jpg = (IMAGETYPE_JPEG == $type);
        $is_png = (IMAGETYPE_PNG  == $type);
        $is_gif = (IMAGETYPE_GIF  == $type);

        if ($cut) {
            $_new_width = $new_width; $_new_height = $new_height;         // СЂР°Р·РјРµСЂС‹ РІС‹С…РѕРґРЅРѕРіРѕ С„Р°Р№Р»Р° ($cut=true)
            $this->calcImageSize($size, $new_width, $new_height, $whitemargin);
        }
        else {
            $this->calcImageSize($size, $new_width, $new_height, 1);
            $_new_width = $new_width; $_new_height = $new_height;
        }

        $gd_src = false;

        if ($is_jpg) {
            $gd_src = imagecreatefromjpeg($src);
        } else if ($is_png) {
            $gd_src = imagecreatefrompng($src);
        } else if ($is_gif) {
            $gd_src = imagecreatefromgif($src);
        } else {
            if (($file_src = file_get_contents($src))) {
                $gd_src = imagecreatefromstring($file_src);
            }
        }

        $cantent = false;

        if (!empty($gd_src)) {

            $gd_dst = false;

            if (!$is_gif) {
                $gd_dst = imagecreatetruecolor($_new_width, $_new_height);
            } else {
                $gd_dst = imagecreate($_new_width, $_new_height);
            }


            if (!empty($gd_dst)) {

                $color = 0;

                if (!$is_jpg) {
                    imagealphablending($gd_dst, false);
                    imagesavealpha($gd_dst,true);
                    
                    $transparent = imagecolorallocate($gd_dst, 0, 0, 0);
                    imagecolortransparent($gd_dst, $black);
                    
                    $color = imagecolorallocatealpha($transparent, 255, 255, 255, 0);
                    imagefilledrectangle($gd_dst, 0, 0, $new_width, $new_height, $transparent);
                } else {
                    $color = imagecolorallocate($gd_dst, 255, 255, 255);
                    imagefill($gd_dst, 0, 0, $color);
                }

                $x = 0;
                $y = 0;

                $geo = (isset($params['gravity'])) ? $params['gravity'] : 'center';

                switch($geo) {
                    case 'north':
                        $x = ceil(($_new_width - $new_width) / 2);
                        break;
                    default:
                        $x = ceil(($_new_width - $new_width) / 2);
                        $y = ceil(($_new_height - $new_height) / 2);
                }

                imagecopyresampled ($gd_dst, $gd_src, $x, $y, 0, 0, $new_width, $new_height, $size[0], $size[1]);

                ob_start();

                if ($is_jpg) {
                    imagejpeg($gd_dst, NULL, $this->th_jpeg_quality);
                } else if ($is_png) {
                    imagepng($gd_dst);
                } else if ($is_gif) {
                    imagegif($gd_dst);
                } else {
                    imagejpeg($gd_dst, NULL, $this->th_jpeg_quality);
                }
                $cantent = ob_get_contents();
                file_put_contents($dst, $cantent);

                ob_end_clean();

                imagedestroy($gd_dst);
            }
            imagedestroy($gd_src);
        }

        return array($ext, $size['mime'], $cantent);
    }

    /**
     * Calculate the dimensions of the image to which it  should be reduced, taking into account the proportions
     *
     * @param array $size - result of getimagesize()
     * @param int &$new_width
     * @param int &$new_height
     */
    function calcImageSize($size, &$new_width, &$new_height, $byLessSide) {
        $small_w = $size[0] <= $new_width;
        $small_h = $size[1] <= $new_height;
        $k = 0;
        if($size[1]>0){
            $k = $size[0]/$size[1];
        }
        if ($k > ($new_width/$new_height)) {
            if ($byLessSide) {
                $new_height = ($small_w) ? $size[1] : ceil($new_width/$k);
                $new_width  = ($small_w) ? $size[0] : $new_width;
            }
            else {
                $new_width  = ($small_h) ? $size[0] : ceil($new_height*$k);
                $new_height = ($small_h) ? $size[1] : $new_height;
            }
        }
        else {
            if ($byLessSide) {
                $new_width  = ($small_h) ? $size[0] : ceil($new_height*$k);
                $new_height = ($small_h) ? $size[1] : $new_height;
            }
            else {
                $new_height = ($small_w) ? $size[1] : ceil($new_width/$k);
                $new_width  = ($small_w) ? $size[0] : $new_width;
            }
        }
    }

    /**
     * Get extension by image type
     * @param $imagetype
     * @return bool|string
     */
    function image_type_to_extension($imagetype)
    {
        if(empty($imagetype)) return false;
        switch($imagetype)
        {
            case IMAGETYPE_GIF    : return 'gif';
            case IMAGETYPE_JPEG   : return 'jpg';
            case IMAGETYPE_PNG    : return 'png';
            case IMAGETYPE_SWF    : return 'swf';
            case IMAGETYPE_PSD    : return 'psd';
            case IMAGETYPE_BMP    : return 'bmp';
            case IMAGETYPE_TIFF_II : return 'tiff';
            case IMAGETYPE_TIFF_MM : return 'tiff';
            case IMAGETYPE_JPC    : return 'jpc';
            case IMAGETYPE_JP2    : return 'jp2';
            case IMAGETYPE_JPX    : return 'jpf';
            case IMAGETYPE_JB2    : return 'jb2';
            case IMAGETYPE_SWC    : return 'swc';
            case IMAGETYPE_IFF    : return 'aiff';
            case IMAGETYPE_WBMP   : return 'wbmp';
            case IMAGETYPE_XBM    : return 'xbm';
            default               : return false;
        }
    }

    /**
     * makePath
     *
     * @param $s = getimagesize()
     * @param $name
     * @return string
     */
    function makePath($s, $name ) {
        $folder1 =  $this->image_dir . (int)$s[0] . '/';
        $folder2 =  $this->image_dir . (int)$s[0] . '/' . (int)$s[1] . '/';
        if( !is_dir( $folder1 ) ) {
            mkdir( $folder1, 0777 );
        }
        @chmod($folder1, 0777);
        if( !is_dir( $folder2 ) ) {
            mkdir( $folder2, 0777 );
        }
        @chmod($folder2, 0777);
        return $folder2 . $name . '.' . $this->image_type_to_extension( $s[2] );
        //return  $this->image_dir . $name . '.' . $this->image_type_to_extension( $s[2] );
    }

    /**
     * Generates thumbnails
     */
    function thumbnailGenerator(){
        $requestedUrl = '';
        if( isset( $_REQUEST['url'] ) ) {
            $requestedUrl = $_REQUEST['url'];
        }elseif(isset($_SERVER['REDIRECT_URL'])){
            $requestedUrl = $_SERVER['REDIRECT_URL'];
        }
        $image = $this->imageValidatePath( $requestedUrl );
        if (!$image) $this->nf("Malformed request");
        $sizes = imgServiceSize($image[0]);
        $generator = true;
        list( $x_size, $y_size ) = explode( '@', $image[1] );//get sizes from name
        $src = $this->getImageDir().$x_size.'/'.$y_size.'/'.$image[1].'.'.$image[2];

        if (!is_file($src)) {//file source not found
            if (is_file($this->getImageDir().'null-'.$image[0].'.jpg')) {
                $src = $this->getImageDir().'null-'.$image[0].'.jpg';
            } else {
                $src = $this->getImageDir()."null.jpg";
            }
            $generated = array('jpg','image/jpeg');

            //check whether there is null.jpg and if it does not generate software
            if (!is_file($src)) {
                $dst = $this->imagePattern( $image[0], 'null', 'jpg' );
                $this->nullGenerate( $sizes, $this->getImageDir().'null-'.$image[0].'.jpg' );
            } else {
                Redirect(sprintf('%s/%s', SITE_PUBLIC_PATH, $this->getImageDir().'null-'.$image[0].'.jpg'));
            }
        }
        else {
            $dst = $this->imagePattern($image[0],$image[1],$image[2], $image[3]);
            // if the image already exists, then give the correct content-type
            $generated = array($image[2],'image/'.str_replace('jpg','jpeg',$image[2]));
        }

        if ($generator && !file_exists($dst)){
            $e = explode('/',str_replace('\\','/',$dst));

            if( !is_dir( $folder = $this->th_dir ) ) {
                mkdir( $folder , 0777 );
                chmod($folder, 0777);
            }
            if( !is_dir( $folder = $folder . $e[count($e)-4] . '/' ) ) {
                mkdir( $folder, 0777 );
                chmod($folder, 0777);
            }
            if( !is_dir( $folder = $folder . $e[count($e)-3] . '/' ) ) {
                mkdir( $folder, 0777 );
                chmod($folder, 0777);
            };
            if( !is_dir( $folder = $folder . $e[count($e)-2] ) ) {
                mkdir( $folder, 0777 );
                chmod($folder, 0777);
            };

            // РїСЂРѕРІРµСЂРєР° РєСЂРёРІС‹С… РєР°СЂС‚РёРЅРѕРє, Сѓ РєРѕС‚РѕСЂС‹С… СЂР°СЃС€РёСЂРµРЅРёРµ РЅРµ СЃРѕРѕС‚РІРµС‚СЃС‚РІСѓРµС‚ РІРЅСѓС‚СЂРµРЅРЅРµРјСѓ С‚РёРїСѓ
            $origtype = $this->getImageType($src, $git);
            $origext = $this->image_type_to_extension($origtype);

            if ($origext != $image[2]) {
                $checkpath = $this->imagePattern( $image[0], $image[1], $origext , $image[3]);
                if (file_exists($checkpath)) { header( "Location: /$checkpath" ); exit; }
            }

            $params = (isset($sizes[4]) && is_array($sizes[4])) ? $sizes[4] : array();

            if ($image[0] == 0 && is_file($src)){
                list($widthOriginal, $heightOriginal) = getimagesize($src);
                $sizes[0] = $widthOriginal;
                $sizes[1] = $heightOriginal;
            }

            $generated = $this->resizeImage($src, $dst, $sizes[0], $sizes[1], $sizes[2], $sizes[3], $params );

            if (!$generated) { die('file generating error'); }
            else{
                //write to the database information about the thumbnail
                $this->addThToDB( $dst, $image[1], $image[0] );
            }

            if ($origext != $image[2]) {
                if (file_exists(ROOT_DIR.$dst)) { header("Location: /$dst"); exit; }
            }
        }

        if (!empty($this->images_cache_lifetime)) {
            header(sprintf('Cache-Control: max-age=%s', $this->images_cache_lifetime), true);
            header(sprintf('Expires: %s GMT', gmdate("D, d M Y H:i:s", time() + $this->images_cache_lifetime)), true);
            header(sprintf('Last-Modified: %s GMT', gmdate("D, d M Y H:i:s")));
            header('Pragma: cache');
        }

        header("Content-type: ".$generated[1]);
        header("Content-length: ".filesize($dst));
        $fp=fopen($dst,'rb');
        fpassthru($fp);
        fclose($fp);
        exit;
    }

    /**
     * Send Http error 400 Bad Request header
     * @param $reason
     */
    function nf($reason) {
        header("HTTP/1.1 400 Bad Request");
        exit($reason);
    }

    /**
     * generate an image with "null"  size
     *
     * @param array $sizes
     * @param null $save_file
     */
    function nullGenerate( $sizes/*array*/ , $save_file = NULL){
        if (empty($sizes[0]) || empty($sizes[1])) {
            $sizes[0] = 1;
            $sizes[1] = 1;
        }
        $im = @imagecreate( $sizes[0], $sizes[1] ) or die("Cannot Initialize new GD image stream");
        $background_color = imagecolorallocate( $im, 255, 255, 255 );
        $border_color = imagecolorallocate( $im, 100, 100, 100 );

        for($i=0; $i<(sqrt($sizes[0]*$sizes[1])/5); $i++){
            $text_color = imagecolorallocate( $im, rand(0,255), rand(0,255), rand(0,255) );
            imagestring( $im, $font_size = 5, rand(-100,$sizes[0]+100), rand(-20,$sizes[1]+20), "Sorry. No image...", $text_color );
        }

        imageRectangle($im, 0, 0, $sizes[0]-1, $sizes[1]-1, $border_color);

        header("Content-type: image/png");
        imagepng($im, $save_file);
        imagedestroy($im);
        die;
    }

    /**
     * Add image to garbage table by ID
     *
     * @param int $id
     * @return bool|string
     */
    /* TODO CHANGE THIS TO DOCTRINE 2 STYLE */
    function setImageToGarbageById($id = 0){
        $r = '';
        if(!empty($id)){
            $r = $this->db->Execute( 'INSERT INTO images_garbage(id, createdate) VALUES (?, NOW())', array($id));
        }
        if(!$r) {
            return 'db error';
        }
        else {
            return false;
        }
    }

    /**
     *
     * Clear data from image garbage(images_garbage) table from DB
     *
     * @param integer $id
     *   id of image from 'image' table to delete it from garbage table
     * @param boolean $is_date
     *   if TRUE then add cause with checking to delete old records by image,
     *   if false then delete by id
     */
    /* TODO CHANGE THIS TO DOCTRINE 2 STYLE */
    function clearImageToGarbageByCause($id = 0, $is_date = false){

        $sql = 'DELETE FROM images_garbage WHERE ';
        $images = array();
        // set first cause = delete images from garbage table and clean images from
        // server
        if($is_date) {
            // set cause for select
            $cause = " TIMESTAMPADD(HOUR, " . ADMIN_GARBAGE_IMAGE_TIME_LIVE . ", createdate)<'" .date("Y-m-d H:i:s")."'";

            // get images to delete from DB garbage table
            $sql_all = 'SELECT id FROM images_garbage WHERE ' . $cause;
            $images = $this->db->select( $sql_all );

            // clean images from table 'images'
            foreach ($images as $image) {
                $this->clearByID($image['id']);
            }
            $sql .= $cause;

            // delete images from 'images_garbage' table
            $r = $this->db->Execute( $sql );
        }
        // simply clean images from garbage table
        else if(!empty($id) && IsInt($id)>0) {

            $sql .= " id=? ";
            $variable = (int)$id;
            $r = $this->db->Execute( $sql, array($id) );
        }

        if(!$r) {
            return 'db error';
        }
        else {
            return false;
        }
    }

    /**
     * Small news garbage
     *
     * @return array
     */
    function getSizesForRedaktor(){
        $sizes = array();
        foreach( $GLOBALS['isSizes'] as $i=>$s ){
            if((int)$i > 2000 && (int)$i < 2999) $sizes[$i] = $s;
        }
        return $sizes;
    }

    /**
     * Set error message in session admin_error,
     *
     * @param string $errorText
     */
    private function _returnError($errorText = ''){
//        clsErrors::getInstance()->set($errorText);
    }
}



