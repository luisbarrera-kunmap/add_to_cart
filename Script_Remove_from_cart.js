<script>
 var xhr = new XMLHttpRequest();
xhr.open("POST", "https://christusmuguerza.com.mx/kunmap/web_service.php", true);
xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
        var respuesta = xhr.responseText;
        console.log(respuesta);
    }
};

var datos = "email=" + encodeURIComponent({{Correo cliente}}) +
    "&sku=" + encodeURIComponent({{SKU eliminado}}) +
    "&type=" + encodeURIComponent('remove');
xhr.send(datos);
  
</script>
