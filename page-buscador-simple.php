<h1>Buscador simple</h1>
<form method="GET">
  <label>Ingresa tu búsqueda</label><br>
  <input type="text" name="busqueda" placeholder="Buscar..."><br>
  <input type="submit" value="Buscar">
</form>

<?php if (isset($_GET['busqueda'])) {
  $productos = new WP_Query([
    'post_type' => 'product',
    's' => $_GET['busqueda']
  ]);
  while ($productos->have_posts()) { $productos->the_post();

    the_title();
    echo '<hr>';

  } wp_reset_postdata();
} ?>