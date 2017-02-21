<?php $this->registerMetaTag(["name"=>"apple-mobile-web-app-capable","content"=>"yes"]);?>
<?php $this->registerMetaTag(["name"=>"apple-mobile-web-app-status-bar-style","content"=>"black"]);?>
<?php $this->registerMetaTag(["name"=>"apple-mobile-web-app-title","content"=>"Maingear"]);?>

<?php $this->registerJsFile("https://code.highcharts.com/highcharts.js"); ?>
<?php $this->registerJsFile("https://code.highcharts.com/highcharts-more.js"); ?>
<?php //$this->registerJsFile("https://code.highcharts.com/modules/exporting.js"); ?>

<?php use yii\helpers\Url; ?>

<?php $this->registerCSSFile("/css/chart.css"); ?>

<?php $chartVariables = array('url'=> Url::to(['site/temps'])); ?>
<?php $this->registerJs("var chartconfig=".json_encode( $chartVariables ),yii\web\View::POS_HEAD); ?>


<div class="chart" id="container" style="min-width: 310px; height: 450px; margin: 0 auto"></div>

<div class="chart" id="container2" style="min-width: 310px; height: 450px; margin: 0 auto"></div>


<?php $this->registerJsFile("/js/tempchart.js" ,['depends' => [\yii\web\JqueryAsset::className()]]); ?>


<?php $this->registerJsFile("/js/dark-unica2.js"); ?>







