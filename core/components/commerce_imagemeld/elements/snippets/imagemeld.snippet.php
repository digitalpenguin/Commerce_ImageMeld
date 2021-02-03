<?php
// Set general product fields as placeholders
$comImageMeld = $modx->getService(
    'commerce_imagemeld',
    'Commerce_ImageMeld',
    $modx->getOption('commerce_imagemeld.core_path', null, $modx->getOption('core_path').'components/commerce_imagemeld/').'model/commerce_imagemeld/',$scriptProperties
);
if (!$comImageMeld instanceof \Commerce_ImageMeld) return '';

// Get snippet param values
$productId = $modx->getOption('productId', $scriptProperties);
$tplCanvas = $modx->getOption('tplCanvas', $scriptProperties,'commerce_imagemeld_canvas');
$tplHiddenInputs = $modx->getOption('tplHiddenInputs', $scriptProperties,'commerce_imagemeld_hidden_inputs');
$tplFileInput = $modx->getOption('tplFileInput', $scriptProperties,'commerce_imagemeld_file_input');
$tplControls = $modx->getOption('tplControls', $scriptProperties,'commerce_imagemeld_controls');
$tplPreview = $modx->getOption('tplPreview', $scriptProperties,'commerce_imagemeld_preview');
$includeJs = $modx->getOption('includeJS', $scriptProperties,true);

// Set as str for JavaScript
$error = 'false';
// Check for a return error
$errorParam = filter_input(INPUT_GET,'cim_err',FILTER_SANITIZE_NUMBER_INT);
if($errorParam) {
    $error = $errorParam;
    $errorMsg = $comImageMeld->getErrorMsg($errorParam);
    $modx->setPlaceholder('cim.error_msg',$errorMsg);
}

// Inject meta
$assetsUrl = $modx->getOption('commerce_imagemeld.assets_url');
$modx->regClientStartupScript('https://cdnjs.cloudflare.com/ajax/libs/fabric.js/4.3.0/fabric.min.js');
$minWidth = $modx->getOption('commerce_imagemeld.min_width');
$minHeight = $modx->getOption('commerce_imagemeld.min_height');
$modx->regClientStartupHTMLBlock("
    <script>
        var cimAssetsUrl = '{$assetsUrl}';
        var cimBgImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAAA3NCSVQICAjb4U/gAAAABlBMVEXMzMz////TjRV2AAAACXBIWXMAAArrAAAK6wGCiw1aAAAAHHRFWHRTb2Z0d2FyZQBBZG9iZSBGaXJld29ya3MgQ1M26LyyjAAAABFJREFUCJlj+M/AgBVhF/0PAH6/D/HkDxOGAAAAAElFTkSuQmCC';
        var cimOverlayImg = '{$scriptProperties['image']}';
        var cimMinWidth = '{$minWidth}';
        var cimMinHeight = '{$minHeight}';
        var cimError = {$error};
    </script>
    ");
if($includeJs) {
    $modx->regClientStartupScript($assetsUrl . 'web/js/imagemeld.js');
}

// Set imagemeld placeholders
$modx->setPlaceholder('cim.product_id',$productId);
$modx->setPlaceholder('cim.canvas',$modx->getChunk($tplCanvas));
$modx->setPlaceholder('cim.hidden_inputs',$modx->getChunk($tplHiddenInputs));
$modx->setPlaceholder('cim.file_input',$modx->getChunk($tplFileInput));
$modx->setPlaceholder('cim.controls',$modx->getChunk($tplControls));
$modx->setPlaceholder('cim.preview',$modx->getChunk($tplPreview));
$modx->setPlaceholder('cim.default_css',$modx->getChunk('commerce_imagemeld_css'));

// Return standard product values as placeholders
$product = $comImageMeld->commerce->adapter->getObject('comProduct', ['id' => $productId, 'removed' => false]);
if ($product instanceof \comProduct) {
    $modx->setPlaceholders($product->toArray());
}

return '';