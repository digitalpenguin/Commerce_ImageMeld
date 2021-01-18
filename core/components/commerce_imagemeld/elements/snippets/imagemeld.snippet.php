<?php
$productId = $modx->getOption('productId', $scriptProperties);
if (!isset($scriptProperties['productId'])) {
    $modx->log(MODX_LOG_LEVEL_ERROR, '[ Commerce_ImageMeld ] productId is missing from snippet call. Unable to load canvas.');
    return '';
}

$tpl = $modx->getOption('tpl', $scriptProperties,'commerce_imagemeld_canvas');
$tplInput = $modx->getOption('tplInput', $scriptProperties,'commerce_imagemeld_input');

$assetsUrl = $modx->getOption('commerce_imagemeld.assets_url');
$modx->regClientStartupScript('https://cdnjs.cloudflare.com/ajax/libs/fabric.js/4.3.0/fabric.min.js');
$modx->regClientStartupHTMLBlock("
    <script>
        var cimAssetsUrl = '{$assetsUrl}';
        var cimBgImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAAA3NCSVQICAjb4U/gAAAABlBMVEXMzMz////TjRV2AAAACXBIWXMAAArrAAAK6wGCiw1aAAAAHHRFWHRTb2Z0d2FyZQBBZG9iZSBGaXJld29ya3MgQ1M26LyyjAAAABFJREFUCJlj+M/AgBVhF/0PAH6/D/HkDxOGAAAAAElFTkSuQmCC';
        var cimOverlayImg = '{$scriptProperties['image']}';
    </script>
    ");
$modx->regClientStartupScript($assetsUrl.'web/js/imagemeld.js');


$modx->regClientHTMLBlock(
    $modx->getChunk('commerce_imagemeld_js',[
        'image_tpl' =>  $scriptProperties['image']
    ])
);

// Load Service
$cim = $modx->getService('commerce_imagemeld','Commerce_ImageMeld',$modx->getOption('commerce_imagemeld.core_path', null, $modx->getOption('core_path') . 'components/commerce_imagemeld/') . 'model/commerce_imagemeld/', $scriptProperties);
if (!($cim instanceof Commerce_ImageMeld)) {
    $modx->log(MODX_LOG_LEVEL_ERROR,'Couldn\'t load Commerce_ImageMeld service!');
    return '';
}

$output = $modx->getChunk($tpl,[
    'cim.product_id'    =>  $productId
]);
$modx->setPlaceholder('cim.commerce_imagemeld_input',$modx->getChunk($tplInput));

return $output;