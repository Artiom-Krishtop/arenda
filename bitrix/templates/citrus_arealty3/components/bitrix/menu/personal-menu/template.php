<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<section class="account-navigation">
    <div class="account-navigation__inner">
        <span class="account-navigation__user">
            <h3 class="account-navigation__user-name"><a class="account-navigation__user-name-link" href="/account/"><?= $USER->GetFirstName();?></a></h3>
        </span>

        <nav class="account-navigation__nav">
            <ul class="account-navigation__list">
                <? foreach ($arResult as $arItem):?>
                    <? if($arItem["DEPTH_LEVEL"] > 1) continue;?>

                    <li class="account-navigation__item <?= $arItem['SELECTED'] ? 'account-navigation__item--active' : ''?>">
                        <a class="account-navigation__link" href="<?=$arItem["LINK"]?>">
                            <div class="account-navigation__link-img">
                                <? if (!empty($arItem['PARAMS']['ICON'])):?>
                                    <?= $arItem['PARAMS']['ICON']; ?>
                                <? endif; ?>
                            </div>
                            <h3 class="account-navigation__link-title">
                                <?=$arItem["TEXT"]?>
                            </h3>
                        </a>
                    </li>
                <? endforeach; ?>
            </ul>
        </nav>
    </div>
</section>
