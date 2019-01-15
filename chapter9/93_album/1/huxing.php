<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
  <head>
    <title>户型鉴赏</title>
    <META charset=utf-8>
    <meta name="author" content="Ste Brennan - Code Computerlove - http://www.codecomputerlove.com/" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;" name="viewport" />
    <meta name="apple-mobile-web-app-capable" content="yes" />

    <link href="css/styles.css" type="text/css" rel="stylesheet" />
    <link href="css/photoswipe.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="js/klass.min.js"></script>
    <script type="text/javascript" src="js/code.photoswipe-3.0.5.min.js"></script>

    <script type="text/javascript">
      (function(window, PhotoSwipe){
        document.addEventListener('DOMContentLoaded', function(){
          var options = {
            preventHide: true,
            getImageSource: function(obj){
              return obj.url;
            },
            getImageCaption: function(obj){
              return obj.caption;
            }
          },

          instance = PhotoSwipe.attach(
                        [
                            { url: 'imghx/image0132.jpg', caption: 'A户型 二室二厅一卫 80.07平方米'},
                            { url: 'imghx/image0092.jpg', caption: 'B户型 二室二厅一卫 84.65平方米'},
                            { url: 'imghx/image0032.jpg', caption: 'C户型 二室二厅二卫 103.89平方米'},
                        ],
                        options
          );

          instance.show(0);
        }, false);
      }(window, window.Code.PhotoSwipe));
    </script>
  </head>
  <body>
    <script type="text/javascript">
      document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
        WeixinJSBridge.call('hideOptionMenu');
      });
      document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
        WeixinJSBridge.call('hideToolbar');
      });
    </script>
  </body>
</html>
