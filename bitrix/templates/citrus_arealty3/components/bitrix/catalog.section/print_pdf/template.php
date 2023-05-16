<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Dompdf\Dompdf;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 */

$this->setFrameMode(true);

if(class_exists(Dompdf::class)){
    ob_start(); ?>
    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="robots" content="noindex, nofollow">
        <style>
            body{
                font-family: 'DejaVu Sans'
            }
            .application-container{width:650px; margin: 0px auto;}
            .application-item{
                width:100%; 
                margin-bottom: 50px;
                page-break-inside: always;
                page-break-after: always;
            }

            .application-item:last-child{
                page-break-inside: auto;
                page-break-after: auto;
            }
            
            .application-item__header{width: 100%;text-align: center;}
            
            .application-item__images{width: 600px; margin: 20px auto}
            .application-item__image{display: inline-block}
            .application-item__image img{
                width: 150px;
                height: 150px;
            }

            .application-item__props{width: 100%;}
        </style>
    </head>
    <body>
        <div class="application-container">
            <? if(!empty($arResult['ITEMS'])):?>
                <? foreach ($arResult['ITEMS'] as $arItem): ?>
                    
                    <div class="application-item">
                        <div class="application-item__header">
                            <h2><?= $arItem['NAME'] ?></h2>
                        </div>

                        <? if (!empty($arItem['APPLICATION_IMAGES'])):?>
                            
                            <div class="application-item__images">
                                <table >
                                    <tbody>
                                        <tr>
                                        
                                        <? foreach ($arItem['APPLICATION_IMAGES'] as $key => $photoId): ?>
                                            <? if($key == 4):?>
                                                </tr><tr>
                                            <? endif;?>
                                            
                                            <td>
                                                <img src="<?= $_SERVER["DOCUMENT_ROOT"] . CFile::GetPath($photoId)?>" width="150px" height="150px">
                                            </td>
        
                                        <? endforeach; ?>
                                        
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        <? endif; ?>
                        <? if(!empty($arItem['PROPERTIES'])):?>
                            <div class="application-item__props">
                                    
                                <? foreach($arItem['PROPERTIES'] as $prop):?>
                                    <? if(!empty($prop['VALUE'])):?>
                                        
                                        <div class="appliaction-item__prop">
                                            <span class="appliaction-item__prop-name"><b><?= $prop['NAME']?></b></span>:
                                            <span class="appliaction-item__prop-value"><?= $prop['VALUE']?></span>
                                        </div>
                                        
                                    <? endif; ?>
                                <? endforeach; ?>
                                    
                            </div>
                        <? endif; ?>
                    </div>
                    
                <? endforeach; ?>
            <? endif; ?>
        </div>
    </body>
    </html>
    <?
    $html = ob_get_contents();
    ob_end_clean();

    $dompdf = new Dompdf();
    $pdfName = 'applications_' . date('Y.m.d H:i:s') . '.pdf'; 

    $dompdf->loadHtml($html);
    $dompdf->set_option('enable_remote', TRUE);
    
    $dompdf->render();

    $canvas = $dompdf->get_canvas();
    $font = $dompdf->getFontMetrics()->get_font("helvetica");

    $canvas->page_text(290, 750, "{PAGE_NUM}", $font, 14, array(0,0,0));
    
    $dompdf->stream($pdfName);
}else {
    throw new \Exception('Class Dompdf is not installed');
}