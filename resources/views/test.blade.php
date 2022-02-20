<!doctype html>
<html>
  <head>
    <title>练习页面 js高级程序设计</title>
    <style>

    </style>
    <script>
      class Handler {
        constructor(){
          
        }
        static changeHash = () => {
            window.location.hash = "#section2"
        }
      }
      window.handler = new Handler()
      window.onload = function(){
        let btnHash = document.querySelector('.btn-hash')
        console.log('onload')
        btnHash.addEventListener("click",function(){console.log(this)});
      }
    </script>
  </head>
  <body>
    <div id="app">
      <div class="container">
        <div class="test-content">
          <button class="btn btn-hash">修改hash值</button>
          <button class="btn"></button>
        </div>
      </div>
    </div>
  </body>
</html>