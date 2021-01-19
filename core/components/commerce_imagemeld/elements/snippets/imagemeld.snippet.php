<?php
$tplCanvas = $modx->getOption('tplCanvas', $scriptProperties,'commerce_imagemeld_canvas');
$tplHiddenInputs = $modx->getOption('tplHiddenInputs', $scriptProperties,'commerce_imagemeld_hidden_inputs');
$tplFileInput = $modx->getOption('tplFileInput', $scriptProperties,'commerce_imagemeld_file_input');
$tplControls = $modx->getOption('tplControls', $scriptProperties,'commerce_imagemeld_controls');
$tplPreview = $modx->getOption('tplPreview', $scriptProperties,'commerce_imagemeld_preview');

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

$modx->setPlaceholder('cim.canvas',$modx->getChunk($tplCanvas));
$modx->setPlaceholder('cim.hidden_inputs',$modx->getChunk($tplHiddenInputs));
$modx->setPlaceholder('cim.file_input',$modx->getChunk($tplFileInput));
$modx->setPlaceholder('cim.controls',$modx->getChunk($tplControls));
$modx->setPlaceholder('cim.preview',$modx->getChunk($tplPreview));
$modx->setPlaceholder('cim.default_css',$modx->getChunk('commerce_imagemeld_css'));

return '';