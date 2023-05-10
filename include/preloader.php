<div class="preloader" hidden>
    <div class="preloader-wrapper">
        <img src="<?= SITE_DIR ?>upload/preloader.gif" alt="preloader">
    </div>
</div>

<style>
    .preloader {
        display: none;
    }

    .preloader.active {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 999999;
        display: flex;
        align-items: center;
        justify-content: center;  
        background: rgb(0 0 0);
        transition: all 0.5s;
        opacity: .75;
    }

    .preloader .preloader-wrapper > img {
        width: 300px;
        height: 300px;
    }
</style>