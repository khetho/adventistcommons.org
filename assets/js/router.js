define(
    [
        '../config/routes.json',
        '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.js',
    ],function(Routes, Router){
        Router.setRoutingData(Routes);

        return Router;
    }
);