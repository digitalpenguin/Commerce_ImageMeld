<?php
/**
 * The main Commerce_ImageMeld service class.
 *
 * @package commerce_imagemeld
 */
class Commerce_ImageMeld {
    public $modx = null;
    public $commerce = null;
    public $namespace = 'commerce_imagemeld';
    public $cache = null;
    public $options = array();


    public function __construct(modX &$modx, array $options = array()) {
        $this->modx =& $modx;
        $this->namespace = $this->getOption('namespace', $options, 'commerce_imagemeld');

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/commerce_imagemeld/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/commerce_imagemeld/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/commerce_imagemeld/');

        // Load some default paths
        $this->options = array_merge(array(
            'namespace' => $this->namespace,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'templatesPath' => $corePath . 'templates/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'connectorUrl' => $assetsUrl . 'connector.php'
        ), $options);

        $this->commerce = $this->modx->getService('commerce','Commerce',MODX_CORE_PATH.'components/commerce/model/commerce/');
        if (!($this->commerce instanceof Commerce)) $this->modx->log(MODX_LOG_LEVEL_ERROR,'Couldn\'t load Commerce service!');
        $this->commerce->adapter->loadLexicon('commerce:default');

        $this->commerce->adapter->loadPackage('commerce_imagemeld', $this->getOption('modelPath'));
        $this->commerce->adapter->loadLexicon('commerce_imagemeld:default');
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = array(), $default = null) {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}");
            }
        }
        return $option;
    }

    /**
     * Returns error message from lexicon based on error parameter 1,2,3
     * @param $errorParam
     * @return mixed
     */
    public function getErrorMsg($errorParam) {
        // Set error message to placeholder
        switch($errorParam) {
            case 1:
                // Not all params submitted
                $errorMsg = $this->commerce->adapter->lexicon('commerce_imagemeld.error.missing_params');
                break;
            case 2:
                // Invalid image
                $errorMsg = $this->commerce->adapter->lexicon('commerce_imagemeld.error.invalid_image_type');
                break;
            case 3:
                // Image too small
                $errorMsg =  $this->commerce->adapter->lexicon('commerce_imagemeld.error.image_too_small');
                break;
            default:
                // Something went wrong
                $errorMsg = $this->commerce->adapter->lexicon('commerce_imagemeld.error');
        }
        return $errorMsg;
    }
}