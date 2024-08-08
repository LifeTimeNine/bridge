<?php

declare(strict_types = 1);

namespace lifetime\bridge;

use DOMDocument;
use DOMElement;
use lifetime\bridge\exception\InvalidDecodeException;
use lifetime\bridge\exception\InvalidResponseException;

/**
 * 工具类
 */
class Tools
{
    /**
     * CURL模拟网络请求
     * @access  public
     * @param   string  $method         请求方法
     * @param   string  $url            请求方法
     * @param   array   $options        请求参数[headers,query,data,ssl_cer,ssl_key]
     * @param   bool    $queryEncode    Query参数编码
     * @return string
     * @throws Exception
     */
    public static function request(string $method, string $url, array $options = [], bool $queryEncode = true): string
    {
        $curl = curl_init();
        // GET参数设置
        if (!empty($options['query'])) {
            $url .= (stripos($url, '?') !== false ? '&' : '?') . ($queryEncode ? http_build_query($options['query']) : Tools::arrToUrl($options['query']));
        }
        // CURL头信息设置
        if (!empty($options['headers'])) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $options['headers']);
        }
        switch (strtoupper($method)) {
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                if (!empty($options['data'])) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $options['data']);
                }
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                if (!empty($options['data'])) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $options['data']);
                }
                break;
            case 'HEAD':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'HEAD');
                break;
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
                break;
        }
        // 证书文件设置
        if (!empty($options['ssl_cert'])) {
            if (file_exists($options['ssl_cert'])) {
                curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
                curl_setopt($curl, CURLOPT_SSLCERT, $options['ssl_cert']);
            } else {
                throw new \Exception("Certificate files that do not exist. --- [ssl_cert]");
            }
        }
        // 证书文件设置
        if (!empty($options['ssl_key'])) {
            if (file_exists($options['ssl_key'])) {
                curl_setopt($curl, CURLOPT_SSLKEYTYPE, 'PEM');
                curl_setopt($curl, CURLOPT_SSLKEY, $options['ssl_key']);
            } else {
                throw new \Exception("Certificate files that do not exist. --- [ssl_key]");
            }
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $content = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        if ($info['http_code'] <> '200' && $info['http_code'] <> '204') {
            throw new InvalidResponseException((string)$content, $info['http_code'], $info);
        }
        return $content;
    }

    /**
     * 数组对象Emoji编译处理
     * @access  public
     * @param array $data
     * @return array
     */
    public static function buildEnEmojiData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::buildEnEmojiData($value);
            } elseif (is_string($value)) {
                $data[$key] = self::emojiEncode($value);
            } else {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * 数组对象Emoji反解析处理
     * @access  public
     * @param array $data
     * @return array
     */
    public static function buildDeEmojiData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::buildDeEmojiData($value);
            } elseif (is_string($value)) {
                $data[$key] = self::emojiDecode($value);
            } else {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * Emoji原形转换为String
     * @access  public
     * @param string $content
     * @return string
     */
    public static function emojiEncode(string $content): string
    {
        return json_decode(preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i", function ($string) {
            return addslashes($string[0]);
        }, json_encode($content)));
    }

    /**
     * Emoji字符串转换为原形
     * @access  public
     * @param   string      $content
     * @return  string
     */
    public static function emojiDecode(string $content): string
    {
        return json_decode(preg_replace_callback('/\\\\\\\\/i', function () {
            return '\\';
        }, json_encode($content)));
    }

    /**
     * 产生随机字符串
     * @access public
     * @param   int     $length     指定字符长度
     * @param   string  $str        字符串前缀
     * @return  string
     */
    public static function createRandomStr(int $length = 32, string $str = ""): string
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $len = strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, $len - 1)];
        }
        return $str;
    }

    /**
     * 获取客户端设备类型
     * @access  public
     * @return  string
     */
    public static function getClientType(): string
    {
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
            return 'IOS';
        }else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android')){
            return 'Android';
        }else{
            return 'Wap';
        }
    }

    /**
     * 响应头转数组
     * @access  public
     * @param   string  $header     响应头
     * @return  array
     */
    public static function header2arr(string $header): array
    {
        $arr = explode("\r\n", $header);
        preg_match('/HTTPS?.{0,4}?/', $arr[0], $prptocol);
        preg_match('/\d{3}/', $arr[0], $code);
        $data = [
            'Protocol' => $prptocol[0],
            'Code' => $code[0]
        ];
        $len = count($arr);
        for($i = 1; $i < $len -1; ++$i) {
            if (empty($arr[$i])) continue;
            $keyVal = explode(':', $arr[$i]);
            $data[trim($keyVal[0])] = trim($keyVal[1]);
        }
        return $data;
    }

    /**
     * 根据文件后缀名获取文件类型
     * @access  public
     * @param   string  $fileName   文件名称
     * @return  string|null
     */
    public static function getMimetype(string $name)
    {
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        return self::$mime_types[$ext] ?? null;
    }
    /**
     * 验证文件类型是否合法
     * @access  public
     * @param   string  $mimeType   文件类型
     * @return  bool
     */
    public static function checkMimeType(string $mimeType): bool
    {
        return in_array($mimeType, self::$mime_types);
    }

    /**
     * 数组转Key，Value形式
     * @access  public
     * @param   array   $data
     * @return  array
     */
    public static function arrToKeyVal(array $data): array
    {
        $newData = [];
        foreach($data as $k => $v)
        {
            if (!empty($k)) {
                $newData[] = ['key' => $k, 'value' => $v];
            }
        }
        return $newData;
    }

    /**
     * 生成Form-data参数
     * @access  public
     * @param   array   $param          参数
     * @param   string  $fileName       文件名称
     * @param   mixed   $fileData       文件数据
     * @param   string  $fileFieldName  文件字段名称
     * @param   array
     */
    public static function buildFormData(array $param, string $fileName = null, $fileData = null, string $fileFiledName = 'file'): array
    {
        $data = [];
        $mimeBoundary = md5(uniqid());
        foreach($param as $k => $v)
        {
            $data[] = "--{$mimeBoundary}";
            $data[] = "Content-Disposition: form-data; name=\"{$k}\"";
            $data[] = "";
            $data[] = $v;
        }
        if (!empty($fileName) && !empty($fileData)) {
            $mimeType = self::getMimetype($fileName);
            if (empty($mimeType)) $mimeType = 'application/octet-stream';
            $data[] = "--{$mimeBoundary}";
            $data[] = "Content-Disposition: form-data; name=\"{$fileFiledName}\"; filename=\"{$fileName}\"";
            $data[] = "Content-Type: {$mimeType}";
            $data[] = "";
            $data[] = $fileData;
        }
        $data[] = "--{$mimeBoundary}--";
        $data[] = '';
        return ["multipart/form-data; boundary={$mimeBoundary}", implode("\r\n", $data)];
    }

    /**
     * mine信息
     * @var array
     */
    private static $mime_types = [
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
        'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
        'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
        'apk' => 'application/vnd.android.package-archive',
        'hqx' => 'application/mac-binhex40',
        'cpt' => 'application/mac-compactpro',
        'doc' => 'application/msword',
        'ogg' => 'audio/ogg',
        'pdf' => 'application/pdf',
        'rtf' => 'text/rtf',
        'mif' => 'application/vnd.mif',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'odc' => 'application/vnd.oasis.opendocument.chart',
        'odb' => 'application/vnd.oasis.opendocument.database',
        'odf' => 'application/vnd.oasis.opendocument.formula',
        'odg' => 'application/vnd.oasis.opendocument.graphics',
        'otg' => 'application/vnd.oasis.opendocument.graphics-template',
        'odi' => 'application/vnd.oasis.opendocument.image',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
        'otp' => 'application/vnd.oasis.opendocument.presentation-template',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'odm' => 'application/vnd.oasis.opendocument.text-master',
        'ott' => 'application/vnd.oasis.opendocument.text-template',
        'oth' => 'application/vnd.oasis.opendocument.text-web',
        'sxw' => 'application/vnd.sun.xml.writer',
        'stw' => 'application/vnd.sun.xml.writer.template',
        'sxc' => 'application/vnd.sun.xml.calc',
        'stc' => 'application/vnd.sun.xml.calc.template',
        'sxd' => 'application/vnd.sun.xml.draw',
        'std' => 'application/vnd.sun.xml.draw.template',
        'sxi' => 'application/vnd.sun.xml.impress',
        'sti' => 'application/vnd.sun.xml.impress.template',
        'sxg' => 'application/vnd.sun.xml.writer.global',
        'sxm' => 'application/vnd.sun.xml.math',
        'sis' => 'application/vnd.symbian.install',
        'wbxml' => 'application/vnd.wap.wbxml',
        'wmlc' => 'application/vnd.wap.wmlc',
        'wmlsc' => 'application/vnd.wap.wmlscriptc',
        'bcpio' => 'application/x-bcpio',
        'torrent' => 'application/x-bittorrent',
        'bz2' => 'application/x-bzip2',
        'vcd' => 'application/x-cdlink',
        'pgn' => 'application/x-chess-pgn',
        'cpio' => 'application/x-cpio',
        'csh' => 'application/x-csh',
        'dvi' => 'application/x-dvi',
        'spl' => 'application/x-futuresplash',
        'gtar' => 'application/x-gtar',
        'hdf' => 'application/x-hdf',
        'jar' => 'application/java-archive',
        'jnlp' => 'application/x-java-jnlp-file',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'ksp' => 'application/x-kspread',
        'chrt' => 'application/x-kchart',
        'kil' => 'application/x-killustrator',
        'latex' => 'application/x-latex',
        'rpm' => 'application/x-rpm',
        'sh' => 'application/x-sh',
        'shar' => 'application/x-shar',
        'swf' => 'application/x-shockwave-flash',
        'sit' => 'application/x-stuffit',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'tar' => 'application/x-tar',
        'tcl' => 'application/x-tcl',
        'tex' => 'application/x-tex',
        'man' => 'application/x-troff-man',
        'me' => 'application/x-troff-me',
        'ms' => 'application/x-troff-ms',
        'ustar' => 'application/x-ustar',
        'src' => 'application/x-wais-source',
        'zip' => 'application/zip',
        'm3u' => 'audio/x-mpegurl',
        'ra' => 'audio/x-pn-realaudio',
        'wav' => 'audio/x-wav',
        'wma' => 'audio/x-ms-wma',
        'wax' => 'audio/x-ms-wax',
        'pdb' => 'chemical/x-pdb',
        'xyz' => 'chemical/x-xyz',
        'bmp' => 'image/bmp',
        'gif' => 'image/gif',
        'ief' => 'image/ief',
        'png' => 'image/png',
        'wbmp' => 'image/vnd.wap.wbmp',
        'ras' => 'image/x-cmu-raster',
        'pnm' => 'image/x-portable-anymap',
        'pbm' => 'image/x-portable-bitmap',
        'pgm' => 'image/x-portable-graymap',
        'ppm' => 'image/x-portable-pixmap',
        'rgb' => 'image/x-rgb',
        'xbm' => 'image/x-xbitmap',
        'xpm' => 'image/x-xpixmap',
        'xwd' => 'image/x-xwindowdump',
        'css' => 'text/css',
        'rtx' => 'text/richtext',
        'tsv' => 'text/tab-separated-values',
        'jad' => 'text/vnd.sun.j2me.app-descriptor',
        'wml' => 'text/vnd.wap.wml',
        'wmls' => 'text/vnd.wap.wmlscript',
        'etx' => 'text/x-setext',
        'mxu' => 'video/vnd.mpegurl',
        'flv' => 'video/x-flv',
        'wm' => 'video/x-ms-wm',
        'wmv' => 'video/x-ms-wmv',
        'wmx' => 'video/x-ms-wmx',
        'wvx' => 'video/x-ms-wvx',
        'avi' => 'video/x-msvideo',
        'movie' => 'video/x-sgi-movie',
        'ice' => 'x-conference/x-cooltalk',
        '3gp' => 'video/3gpp',
        'ai' => 'application/postscript',
        'aif' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'asc' => 'text/plain',
        'atom' => 'application/atom+xml',
        'au' => 'audio/basic',
        'bin' => 'application/octet-stream',
        'cdf' => 'application/x-netcdf',
        'cgm' => 'image/cgm',
        'class' => 'application/octet-stream',
        'dcr' => 'application/x-director',
        'dif' => 'video/x-dv',
        'dir' => 'application/x-director',
        'djv' => 'image/vnd.djvu',
        'djvu' => 'image/vnd.djvu',
        'dll' => 'application/octet-stream',
        'dmg' => 'application/octet-stream',
        'dms' => 'application/octet-stream',
        'dtd' => 'application/xml-dtd',
        'dv' => 'video/x-dv',
        'dxr' => 'application/x-director',
        'eps' => 'application/postscript',
        'exe' => 'application/octet-stream',
        'ez' => 'application/andrew-inset',
        'gram' => 'application/srgs',
        'grxml' => 'application/srgs+xml',
        'gz' => 'application/x-gzip',
        'htm' => 'text/html',
        'html' => 'text/html',
        'ico' => 'image/x-icon',
        'ics' => 'text/calendar',
        'ifb' => 'text/calendar',
        'iges' => 'model/iges',
        'igs' => 'model/iges',
        'jp2' => 'image/jp2',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'kar' => 'audio/midi',
        'lha' => 'application/octet-stream',
        'lzh' => 'application/octet-stream',
        'm4a' => 'audio/mp4a-latm',
        'm4p' => 'audio/mp4a-latm',
        'm4u' => 'video/vnd.mpegurl',
        'm4v' => 'video/x-m4v',
        'mac' => 'image/x-macpaint',
        'mathml' => 'application/mathml+xml',
        'mesh' => 'model/mesh',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'mov' => 'video/quicktime',
        'mp2' => 'audio/mpeg',
        'mp3' => 'audio/mpeg',
        'mp4' => 'video/mp4',
        'mpe' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mpga' => 'audio/mpeg',
        'msh' => 'model/mesh',
        'nc' => 'application/x-netcdf',
        'oda' => 'application/oda',
        'ogv' => 'video/ogv',
        'pct' => 'image/pict',
        'pic' => 'image/pict',
        'pict' => 'image/pict',
        'pnt' => 'image/x-macpaint',
        'pntg' => 'image/x-macpaint',
        'ps' => 'application/postscript',
        'qt' => 'video/quicktime',
        'qti' => 'image/x-quicktime',
        'qtif' => 'image/x-quicktime',
        'ram' => 'audio/x-pn-realaudio',
        'rdf' => 'application/rdf+xml',
        'rm' => 'application/vnd.rn-realmedia',
        'roff' => 'application/x-troff',
        'sgm' => 'text/sgml',
        'sgml' => 'text/sgml',
        'silo' => 'model/mesh',
        'skd' => 'application/x-koan',
        'skm' => 'application/x-koan',
        'skp' => 'application/x-koan',
        'skt' => 'application/x-koan',
        'smi' => 'application/smil',
        'smil' => 'application/smil',
        'snd' => 'audio/basic',
        'so' => 'application/octet-stream',
        'svg' => 'image/svg+xml',
        't' => 'application/x-troff',
        'texi' => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'tr' => 'application/x-troff',
        'txt' => 'text/plain',
        'vrml' => 'model/vrml',
        'vxml' => 'application/voicexml+xml',
        'webm' => 'video/webm',
        'webp' => 'image/webp',
        'wrl' => 'model/vrml',
        'xht' => 'application/xhtml+xml',
        'xhtml' => 'application/xhtml+xml',
        'xml' => 'application/xml',
        'xsl' => 'application/xml',
        'xslt' => 'application/xslt+xml',
        'xul' => 'application/vnd.mozilla.xul+xml',
    ];

    /**
     * 结束响应
     * @access  public
     * @return  void
     */
    public static function endResponse()
    {
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } else {
            exit;
        }
    }

    /**
     * 驼峰转下划线
     * @access  public
     * @param   string  $str
     * @return string
     */
    public static function hump2underline(string $str): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $str));
    }

    /**
     * 数组转url(不进行URL编码)
     * @access  public
     * @param   array   $arr    源数据
     * @return string
     */
    public static function arrToUrl(array $arr): string
    {
        $buff = [];
        foreach ($arr as $key => $value) {
            if (is_null($value)) {
                $buff[] = $key;
            } else {
                $buff[] = "{$key}={$value}";
            }
        }
        return implode('&', $buff);
    }

    /**
     * XML转数组
     * @access  public
     * @param   string  $xml    XML数据
     * @return  array
     */
    public static function xmlToArr(string $xml): array
    {
        try {
            $xml = simplexml_load_string($xml);
        } catch (\Throwable $th) {
            throw new InvalidDecodeException($th->getMessage(), $th->getCode());
        }
        return json_decode(json_encode($xml), true);
    }

    /**
     * 数组转XML
     * @access  public
     * @param   array   $arr        数组数据
     * @param   string  $rootName   根节点名称
     * @return  string
     */
    public static function arrToXml(array $arr, string $rootName = 'xml'): string
    {
        $call = function (DOMDocument $xml, DOMElement $el, array $arr, callable $call) {
            foreach($arr as $key => $value) {
                if (is_array($value)) {
                    if (count(array_diff_key($value, array_values($value))) > 0) {
                        $chlid = $xml->createElement($key);
                        call_user_func_array($call, [$xml, $chlid, $value, $call]);
                        $el->appendChild($chlid);
                    } else {
                        foreach($value as $item) {
                            if (is_array($item)) {
                                $chlid = $xml->createElement($key);
                                call_user_func_array($call, [$xml, $chlid, $item, $call]);
                            } else {
                                $chlid = $xml->createElement($key, $item);
                            }
                            $el->appendChild($chlid);
                        }

                    }
                } else {
                    $chlid = $xml->createElement($key, (string)$value);
                    $el->appendChild($chlid);
                }
            }
        };
        $xml = new DOMDocument('1.0', 'UTF-8');
        $root = $xml->createElement($rootName);
        call_user_func_array($call, [$xml, $root, $arr, $call]);
        $xml->appendChild($root);
        return $xml->saveXML();
    }
}
