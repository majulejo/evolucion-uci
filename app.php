<?php
session_start();
//app.php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// Resto del código de app.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="robots" content="noindex, nofollow">
  <meta name="referrer" content="no-referrer-when-downgrade">
  <meta name="description" content="Registro de informes  de Evolución UCI.">
  <meta name="author" content="Equipo de Desarrollo - Evolución UCI">    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
  <link rel="icon" href="img/evo-uci.png?v=3" type="image/png" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <title>Evolución de Enfermería</title>



  <style>
    :root {
      --fuente: #2e2925;
      --pantone: #368f3f;
      --principal: #92c99b;
      --hover: #d9ebd8;
      --borde: #4fa66a;
      --pantone15: #79b47f;
      --btn: #489950;
      --text-area:#d1e5d3;
    }

    * {
      margin: 0;
      padding: 0;
      outline: none;
      box-sizing: border-box;
    }

    body {
      font-family: "Montserrat", sans-serif;
      margin: 20px;
      padding: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      background-color: var(--principal);
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 24px;
      color: var(--fuente);
    }

    .box-selector {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 20px;
    }

    .box-selector button {
      flex: 0 0 auto;
      /* Que no estiren ni encojan */
      margin: 4px;
      /* Espaciado uniforme */
    }


    .box-selector button {
      padding: 10px 15px;
      font-size: 14px;
      font-weight: bold;
      cursor: pointer;
      border: none;
      border-radius: 5px;
      background-color: var(--pantone15);
      color: white;
      min-width: 80px;
    }

    .box-selector button:hover {
      background-color: white;
      color: var(--fuente);
      transition: all 0.5s ease;
    }

    .box-selector button.active {
      background-color: var(--pantone);
      box-shadow: 0 0 5px #0000004d;
      transition: all 0.5s ease;
    }

    .formulario {
      width: 100%;
      max-width: 600px;
      margin-bottom: 20px;
    }

    .campo {
      margin-bottom: 15px;
      text-align: center;
    }

    .campo label {
      font-weight: bold;
      display: block;
      margin-bottom: 5px;
      font-size: 14px;
      color: var(--fuente);
    }

    .campo textarea {
      width: 95%;
      min-height: 80px;
      padding: 10px;
      font-size: 14px;
      border: 1px solid var(--borde);
      border-radius: 5px;
      resize: vertical;
      background-color: var(--text-area);
      color: var(--fuente);
      outline: none;
      overflow: auto;
      overflow-y: hidden;
    }

    .campo textarea:disabled {
      background-color: var(--principal) !important;
      border: 1px solid var(--borde) !important;
      color: #6c757d !important;
      cursor: not-allowed;
    }

    .contador-global {
      text-align: right;
      font-weight: 400;
      font-size: 10px;
      color: var(--fuente);
      margin-top: -10px;
      margin-bottom: 10px;
    }

    /* Contenedores de botones principales y de imprimir */
    .main-actions-container,
    .print-actions-container {
      display: flex;
      justify-content: center;
      /* Centra los botones */
      margin-top: 20px;
      flex-wrap: wrap;
      gap: 10px;
      width: 100%;
      max-width: 600px;
      /* Ancho de referencia */
    }

    .main-actions-container button,
    .print-actions-container button,
    .print-actions-container .btn-alternativo {
      padding: 10px 15px;
      font-size: 14px;
      cursor: pointer;
      border: none;
      border-radius: 5px;
      background-color: var(--btn);
      color: #ffffff;
      flex: 1;
      /* Permite que los botones crezcan y se encojan */
      min-width: 120px;
      /* Mínimo para evitar que se hagan demasiado pequeños */
      font-weight: bold;
      transition: background-color 0.3s ease-in-out;
    }

    .main-actions-container button:hover,
    .print-actions-container button:hover,
    .print-actions-container .btn-alternativo:hover {
      background-color: var(--pantone15);
      color: var(--fuente);
    }

    .print-actions-container .btn-alternativo {
      background-color: white;
      color: var(--pantone);
    }

    /* Estilo para los iconos dentro de los botones en pantallas grandes */
    .main-actions-container button i,
    .print-actions-container button i,
    .eliminar-buttons-row .eliminarInforme i,
    .copiar-btn i {
      font-size: 16px;
      /* Tamaño pequeño para los iconos en desktop */
      margin-right: 8px;
      /* Espacio entre icono y texto */
    }

    /* Estilo general para el resultado del informe */
.resultado {
  margin-top: 20px;
  padding: 20px;
  font-family: "Montserrat", sans-serif;
  font-size: 12px;
  line-height: 1.2;
  display: none;
  background-color: #f5f5f5;
  width: 100%;
  max-width: 700px;
  box-sizing: border-box;
  word-wrap: break-word;
  white-space: pre-wrap;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Cabecera del informe (BOX y turno) */
.resultado .cabecera {
  font-size: 14px;
  font-weight: 700;
  line-height: 1.3;
  margin-bottom: 4px; /* Separación mínima */
}

/* Etiqueta fuerte (label) */
.resultado .label-strong {
  font-weight: 700;
}

/* Contenido del informe */
.resultado p {
  margin: 2px 0 !important;
  line-height: 1.2;
}

/* Para imprimir */
.imprimir-cabecera {
  margin: 0;
  font-weight: 700;
  font-size: 14px;
  line-height: 1.3;
  margin-bottom: 4px; /* Separación mínima */
}

.imprimir-label-strong {
  font-weight: 700;
}

.imprimir-texto-normal {
  font-weight: normal;
}

    }

   
    /* Opcional: Para asegurar que se aplique en la vista previa también */
    .resultado .imprimir-texto-normal {
      font-weight: normal;
    }

    .no-especificado {
      font-style: italic;
      color: #666;
    }


    

    .imprimir-parrafo {
      margin: 0;
      line-height: 1.2;
      /* hace que cada línea quede “muy pegada” a la siguiente */
    }

    


   /* MENSAJE PRINCIPAL (centrado bajo icono) */
#mensaje-box-seleccionado {
  text-align: center;
  font-weight: bold;
  margin-top: 5px;
  margin-bottom: 20px;
  color: white;
  background-color: var(--text-area);
  border: 1px solid var(--fuente);
  border-radius: 8px;
  padding: 8px 16px;
  font-size: 16px;
  display: none;
  width: fit-content;
  margin-left: auto;
  margin-right: auto;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

/* INDICADOR FLOTANTE (a la izquierda) */
#box-indicador-flotante {
  position: absolute;
  left: 2%;
  top: 40px;
  z-index: 1000;
  display: none;
  padding: 6px 12px;
  font-size: 16px;
  font-weight: bold;
  color: var(--fuente);
  border: 2px solid var(--fuente);
  border-radius: 10px;
  background-color: #fff;
  box-sizing: border-box;
  transition: top 0.3s ease;
  white-space: nowrap;
}

/* 🔽 Versión más compacta para móviles */
@media (max-width: 768px) {
  #box-indicador-flotante {
    font-size: 8px;
    padding: 4px 8px;
    border-width: 1px;
    border-radius: 6px;
    left: 1%;
  }
}



    #mensaje-turno {
      text-align: center;
      font-weight: bold;
      margin-top: 10px;
      color: var(--fuente);
      display: none;
      font-size: 14px;
    }

    /* Estilo para el mensaje de confirmación temporal */
    #mensajeConfirmacion {
      text-align: center;
      padding: 10px;
      margin-top: 10px;
      background-color: #d4edda;
      /* Verde claro */
      color: #155724;
      /* Verde oscuro */
      border: 1px solid #c3e6cb;
      border-radius: 5px;
      font-weight: bold;
      display: none;
      /* Oculto por defecto */
      width: 100%;
      max-width: 600px;
      box-sizing: border-box;
    }

    /* Contenedor principal de las filas de select/botones de eliminar */
    .fecha-container {
      font-weight: bold;
      margin-top: 20px;
      /* Separación con los botones de arriba */
      color: var(--fuente);
      position: relative;
      overflow: visible !important;
      z-index: 1000;
      display: flex;
      flex-direction: column;
      /* Apila las filas verticalmente */
      align-items: center;
      /* Centra las filas horizontalmente */
      gap: 10px;
      /* Espacio vertical entre las filas */
      width: 100%;
      max-width: 600px;
      /* Alineado con el formulario y el grupo de botones principal */
    }

    /* Fila para el selector "Seleccionar Informe Guardado" */
    .select-row {
      display: flex;
      justify-content: center;
      /* Centra el selector */
      align-items: center;
      /* Alinea los elementos verticalmente */
      width: 100%;
      max-width: 600px;
      /* Mismo ancho que el contenedor principal de botones */
      gap: 8px;
      /* Espacio entre el icono y el select */
    }

    /* Contenedor para los dos botones de eliminar */
    .eliminar-buttons-row {
      display: flex;
      flex-direction: row;
      /* Coloca los botones en fila */
      justify-content: center;
      /* Centra los botones horizontalmente */
      gap: 10px;
      /* Espacio entre los botones */
      width: 100%;
      max-width: 600px;
      /* Mismo ancho que el contenedor principal de botones */
      flex-wrap: wrap;
      /* Permite que los botones se envuelvan en pantallas pequeñas */
    }

    /* Estilo para el select y los botones de eliminar (sin margin en los lados) */
    #informesGuardados,
    .eliminar-buttons-row .eliminarInforme {
      padding: 10px 15px;
      font-size: 14px;
      cursor: pointer;
      border: none;
      border-radius: 5px;
      background-color: var(--pantone15);
      /* Color de fondo consistente */
      color: var(--fuente);
      /* Color de texto consistente */
      flex: 1;
      /* Permite que los elementos crezcan y se encojan */
      min-width: 140px;
      /* Un mínimo para que no se hagan demasiado pequeños */
      font-weight: bold;
      transition: background-color 0.3s ease-in-out;
      margin: 0;
    }

    #informesGuardados:hover,
    .eliminar-buttons-row .eliminarInforme:hover {
      background-color: var(--btn);
      /* Hover consistente */
      color: #fff;
      /* Color de texto en hover consistente */
    }

    /* mensaje de aviso 1200 caracteres */
    #aviso-1200 {
      position: sticky;
      top: 50%;
      z-index: 100;
      background-color: #ffefc1;
      color: #b35900;
      font-weight: bold;
      padding: 10px 15px;
      margin-top: 10px;
      border-left: 5px solid #ffcc00;
      border-radius: 4px;
      font-size: 13px;
      animation: fadeIn 0.5s ease-in-out;
      text-align: center;
      max-width: 600px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-5px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .contador-aviso {
      color: var(--pantone) !important;
    }

    .contador-alerta {
      color: red !important;
      font-size: 14px;
    }

    /* botón copiar informe */
    .copiar-btn {
      padding: 10px 15px;
      font-size: 14px;
      cursor: pointer;
      border: none;
      border-radius: 5px;
      background-color: var(--btn);
      color: white;
      font-weight: bold;
      transition: background-color 0.3s ease-in-out;
      margin-top: 10px;
      min-width: 120px;
    }

    .copiar-btn:hover {
      background-color: var(--pantone15);
    }

    /* listado informes guardados */
    /* NOTA: #informesGuardados directas son menos efectivas con Choices.js */
    #informesGuardados optgroup {
      font-weight: bold;
      line-height: 1.6;
      color: white;
      background-color: var(--pantone15);
      padding: 4px 12px;
    }

    #informesGuardados option {
      padding: 6px 12px;
      white-space: nowrap;
    }

    /* no especificado */
    .no-especificado {
      font-style: italic;
      color: #666;
      /* gris medio */
    }

    /* Estilo para el select nativo */
    #informesGuardados {
      /* Restablecer estilos de Choices.js que ya no son necesarios */
      -webkit-appearance: none;
      /* Elimina estilos nativos en Webkit */
      -moz-appearance: none;
      /* Elimina estilos nativos en Firefox */
      appearance: none;
      /* Elimina estilos nativos */
      background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%20viewBox%3D%220%200%20292.4%20292.4%22%3E%3Cpath%20fill%3D%22%232e2925%22%20d%3D%22M287%20197.9c-3.6%203.6-7.8%205.4-12.1%205.4s-8.5-1.8-12.1-5.4L146.2%2091.2%2029.6%20197.9c-3.6%203.6-7.8%205.4-12.1%205.4s-8.5-1.8-12.1-5.4c-7.2-7.2-7.2-18.4%200-25.6L134.1%206.5c7.2-7.2%2018.4-7.2%2025.6%200l108.7%20108.7c7.2%207.2%207.2%2018.4%200%2025.6z%22%2F%3E%3C%2Fsvg%3E");
      background-repeat: no-repeat;
      background-position: right 10px center;
      background-size: 12px;
      padding-right: 30px;
      /* Espacio para la flecha */
      text-align: center;
      /* Centra el texto */
      text-align-last: center;
      /* Para IE/Edge */
      padding-left: 30px;
      /* Equilibra el padding derecho para centrar visualmente */
    }

    /* Cuando el select está abierto, la lista de opciones no tendrá scroll */
    #informesGuardados:focus::-webkit-scrollbar,
    #informesGuardados:active::-webkit-scrollbar,
    #informesGuardados:hover::-webkit-scrollbar {
      width: 0;
      background: transparent;
      /* make scrollbar transparent */
    }

    /* Para Firefox */
    #informesGuardados {
      scrollbar-width: none;
      /* Firefox */
    }

    #informesGuardados option {
      white-space: normal;
      /* Permite que el texto se envuelva */
    }

    /* Estilo del icono para el select (ahora oculto por defecto) */
    .select-icon {
      font-size: 18px;
      /* Tamaño del icono */
      color: var(--fuente);
      /* Color del icono */
      display: none;
      /* Ocultar por defecto */
    }

    /* icono_balance */
    .icono-container {
      position: relative;
      display: inline-block;
      width: 30px;
      /* Ajusta el tamaño del icono */
      height: 30px;
      margin-bottom: 10px;
    }

    .icono {
      position: absolute;
      width: 100%;
      height: 100%;
      transition: opacity 0.5s ease-in-out;
      /* Transición suave */
    }

    /* Mostrar el icono verde por defecto */
    .icono.verde {
      opacity: 1;
    }

    /* Ocultar el icono azul por defecto */
    .icono.azul {
      opacity: 0;
    }

    /* Cuando se hace hover, la imagen verde desaparece y la azul aparece */
    .icono-container:hover .verde {
      opacity: 0;
    }

    .icono-container:hover .azul {
      opacity: 1;
    }

    /* Fin icono_balance */


   

    /* =============================== */
    /* FOOTER */
    /* =============================== */

    footer {
      color: var(--fuente);
      font-size: 10px;
      font-weight: bold;
      text-align: center;
      padding: 5px 0px 0px;
      margin: 20px auto 0px;
      width: 100%;
      z-index: 50;
      border-top: 1px solid var(--pantone);
    }

    /* fin del footer */
    
    
    /* MEDIA QUERIES PARA RESPONSIVIDAD */
    @media (max-width: 768px) {

      /* Ocultar los botones de imprimir en móvil */
      .print-actions-container {
        display: none;
      }

      /* Ocultar el botón de copiar informe en móvil */
      #copiarInformeBtn {
        display: none !important;
        /* Se mantiene oculto en móvil, !important para asegurar */
      }

      /* Contenedor de botones principales (Generar, Borrar) */
      .main-actions-container {
        flex-direction: row;
        /* Organizar en fila */
        justify-content: space-evenly;
        /* Espacio entre los elementos */
        width: 100%;
        max-width: 90%;
        /* Ajusta el ancho máximo para móviles */
        align-items: center;
        /* Centra los elementos verticalmente */
        gap: 20px;
        /* Espacio más pequeño entre botones en fila */
      }

      /* Contenedor de botones de eliminar */
      .eliminar-buttons-row {
        flex-direction: row;
        /* Ya está en fila, asegurar */
        justify-content: space-evenly;
        /* Espacio entre los elementos */
        width: 100%;
        max-width: 90%;
        align-items: center;
        gap: 20px;
        /* Espacio más pequeño entre botones en fila */
      }

      /* Ocultar el texto de los botones y mostrar solo los iconos */
      .main-actions-container button,
      .eliminar-buttons-row .eliminarInforme {
        text-indent: -9999px;
        /* Mueve el texto fuera de la vista */
        overflow: hidden;
        /* Oculta el texto que se desborda */
        white-space: nowrap;
        /* Evita que el texto se envuelva */
        padding: 10px;
        /* Ajusta el padding para centrar el icono */
        min-width: 50px;
        /* Haz los botones más pequeños */
        max-width: 60px;
        /* Limita el ancho para que sean más cuadrados */
        display: flex;
        /* Usa flexbox para alinear icono y texto */
        align-items: center;
        justify-content: center;
        /* Centra el contenido horizontalmente */
      }

      /* Estilo para los iconos dentro de los botones en móvil */
      .main-actions-container button i,
      .eliminar-buttons-row .eliminarInforme i {
        font-size: 20px;
        /* Tamaño del icono */
        margin-right: 0;
        /* Elimina el margen a la derecha del icono ya que no hay texto */
        text-indent: 0;
        /* Asegura que el icono no se vea afectado por el text-indent del padre */
      }

      /* Ajuste específico para el icono de "Imprimir en Turno Alternativo" si tiene dos iconos */
      .print-actions-container .btn-alternativo i:first-child {
        margin-right: 4px;
      }

      /* Disminuir tamaño de fuente para el select de informes guardados en móvil */
      #informesGuardados {
        font-size: 11px;
        /* Reduce el tamaño de fuente del select */
      }

      #informesGuardados optgroup {
        font-size: 9px;
        /* Reduce el tamaño de fuente de los grupos de opciones */
      }

      #informesGuardados option {
        font-size: 9px !important;
        /* Reduce el tamaño de fuente de las opciones */
      }

      /* Asegura que el icono del select esté oculto en móvil */
      .select-icon {
        display: none;
      }
    }


    
    



    /* =============================== */
    /* MEDIA PRINT */
    /* =============================== */

    /* ESTILOS DE IMPRESIÓN MEJORADOS */
    @media print {
  @page {
    size: A4 portrait;
    margin: 0;
  }

  body * {
    visibility: hidden !important;
  }

  html, body {
    height: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
  }

  #resultado, #resultado * {
    visibility: visible !important;
  }

  #resultado {
    position: absolute !important;
    left: 3cm !important;
    right: 1.5cm !important;
    padding: 0 !important;
    margin: 0 !important;
    background: white !important;
    box-shadow: none !important;
    border: none !important;
    line-height: 1.2 !important;
    box-sizing: border-box;
    white-space: pre-wrap;
    word-wrap: break-word;
    max-height: calc(297mm - 4cm) !important; /* 297mm alto de A4 menos 2cm top + 2cm bottom */
    overflow: hidden !important;
  }

  #resultado p {
    margin: 0 !important;
    padding: 0 !important;
    line-height: 1.1 !important;
  }

  /* Posicionamiento para turno diurno */
  #resultado.diurno-print {
    top: 2cm !important;
    bottom: auto !important;
  }

  /* Posicionamiento para turno nocturno */
  #resultado.nocturno-print {
    bottom: 2cm !important;
    top: auto !important;
  }
}

/* =========================================== */
/*  POSICIÓN Y ESTILO FINAL  #logoutBtn        */
/*  (debe ir al FINAL de la hoja de estilos)   */
/* =========================================== */
#logoutBtn{
  position:fixed !important;   /* saca el botón del flujo y lo fija */
  top:16px   !important;       /* distancia al borde superior       */
  right:20px !important;       /* al borde derecho                  */
  left:auto  !important;
  bottom:auto!important;
  transform:none!important;

  /* apariencia coherente con la paleta verde */
  background:var(--pantone15);
  color:#fff;
  border:1px solid var(--borde);
  padding:8px 14px;
  font:700 .9rem/1 "Montserrat",sans-serif;
  display:flex;
  align-items:center;
  gap:.5rem;
  border-radius:8px;
  box-shadow:0 2px 4px rgba(0,0,0,.15);
  cursor:pointer;
  transition:background .25s, box-shadow .25s;
}

/* icono un poco mayor */
#logoutBtn i{font-size:1.1rem;line-height:1;}

/* hover / focus */
#logoutBtn:hover,
#logoutBtn:focus-visible{
  background:#fff;
  color:var(--fuente);
  box-shadow:0 2px 6px rgba(0,0,0,.25);
  outline:none;
}

/* oculta solo la palabra en pantallas pequeñas */
@media(max-width:480px){
  #logoutBtn span{display:none;}
}

  </style>
</head>

<body>
 

  <h1>Evolución de Enfermería</h1>
  <div id="contenidoApp" style="display: none"></div>
  <div class="box-selector" id="boxSelector"></div>
  

  <a href="https://jolejuma.es/evolucion-uci/index_balance_uci.php" class="icono-container">
    <img src="img/balance_verde.png" alt="Balance" class="icono verde" />
    <img src="img/balance_azul.png" alt="Balance Azul" class="icono azul" />
  </a>
  

  <!-- Mensaje centrado -->
<div id="mensaje-box-seleccionado">
  Ha seleccionado el Box <span id="numero-box-seleccionado-msg"></span>
</div>

<!-- Indicador discreto flotante -->
<div id="box-indicador-flotante">
  <span id="numero-box-seleccionado-fijo"></span>
</div>


  <div id="mensajeConfirmacion"></div>
  <form class="formulario" id="formulario">
    <div class="campo">
  <label for="neurologico">1. NEUROLÓGICO</label>
  <textarea id="neurologico" disabled></textarea>
</div>
<div class="campo">
  <label for="cardiovascular">2. CARDIOVASCULAR</label>
  <textarea id="cardiovascular" disabled></textarea>
</div>
    <div class="campo">
      <label for="respiratorio">3. RESPIRATORIO</label>
      <textarea id="respiratorio" disabled></textarea>
    </div>
    <div class="campo">
      <label for="renal">4. RENAL</label>
      <textarea id="renal" disabled></textarea>
    </div>
    <div class="campo">
      <label for="gastrointestinal">5. GASTROINTESTINAL</label>
      <textarea id="gastrointestinal" disabled></textarea>
    </div>
    <div class="campo">
      <label for="nutricional">6. NUTRICIONAL/METABÓLICO</label>
      <textarea id="nutricional" disabled></textarea>
    </div>
    <div class="campo">
      <label for="termorregulacion">7. TERMORREGULACIÓN</label>
      <textarea id="termorregulacion" disabled></textarea>
    </div>
    <div class="campo">
      <label for="piel">8. PIEL</label>
      <textarea id="piel" disabled></textarea>
    </div>
    <div class="campo">
      <label for="otros">9. OTROS</label>
      <textarea id="otros" disabled></textarea>
    </div>
    <div class="campo">
      <label for="especial">10. ESPECIAL VIGILANCIA</label>
      <textarea id="especial" disabled></textarea>
    </div>

    <div class="contador-global">
      <strong>Total de caracteres utilizados:</strong>
      <span id="contador-total">0</span> /
      <span id="total-maximo">1200</span>
    </div>
  </form>



  <div class="main-actions-container">
    <button onclick="generarInforme()">
      <i class="fas fa-file-export"></i> Generar Informe
    </button>
    <button onclick="borrarDatos()">
      <i class="fa-solid fa-arrows-turn-right fa-flip-horizontal"></i>
      Borrar Datos
    </button>
    <button id="copiarInformeBtn" onclick="copiarInforme()" class="copiar-btn" style="display: none">
      <i class="fas fa-copy"></i> Copiar Informe
    </button>
  </div>

  <div class="print-actions-container">
    <button onclick="imprimirAuto()">
      <i class="fas fa-print"></i> Imprimir
    </button>
    <button class="btn-alternativo" onclick="imprimirAlternativo()">
      <i class="fas fa-print"></i>
      <i class="fas fa-exchange-alt"></i> Imprimir en Turno Alternativo
    </button>
  </div>

  <div class="fecha-container">
    <div class="select-row">
      <select id="informesGuardados" onchange="cargarInformeDesdeLista(this)">
        <option value="">-- Seleccionar Informe Guardado --</option>
      </select>


    </div>
    <div class="eliminar-buttons-row">
      <button class="eliminarInforme" onclick="eliminarInforme()">
        <i class="fas fa-trash-can"></i> Eliminar Informe
      </button>
      <button class="eliminarInforme" onclick="eliminarInformesDeBox()">
        <i class="fas fa-broom"></i> Eliminar Informes del Box
      </button>
    </div>
  </div>

  <div class="resultado" id="resultado"></div>

  <!-- área oculta que usaremos solo para imprimir -->
  <div id="printArea" style="display:none"></div>


  <div id="mensaje-turno"></div>
  <!-- Botón de cerrar sesión -->
  <button id="logoutBtn" onclick="cerrarSesion()">
    <i class="fa-solid fa-lock"></i>
    <span>CERRAR SESIÓN</span>
  </button>
  <footer>
    <p>
      &copy; 2025 Informe Evolución Enfermería. Todos los derechos
      reservados.
      <br>C.G.Francisco Manuel--M.G.José Antonio--M.M. Francisco Javier<br>
    </p>

  </footer>
  </div>
  <!-- ==================   JS principal   ================== -->

  <script>
      
   // Variables globales
let selectedBox = null;
let currentUserId = null;
let currentInformeId = null;
let currentInformeBox = null;

const DEBOUNCE_MS = 800;
const campos = [
  "neurologico", "cardiovascular", "respiratorio", "renal",
  "gastrointestinal", "nutricional", "termorregulacion",
  "piel", "otros", "especial"
];

// Función para eliminar el draft
async function eliminarDraft() {
  if (!selectedBox) {
    console.error("No se ha seleccionado ningún box.");
    return;
  }

  try {
    const response = await fetch('clear_draft.php', {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ box: selectedBox })
    });
    const data = await response.json();
    console.log("Respuesta del servidor al eliminar draft:", data);

    if (!data.success) {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error("Error al eliminar draft:", error.message);
  }
}

// Evento DOMContentLoaded
document.addEventListener("DOMContentLoaded", async () => {
  console.log("DOM cargado. Generando botones Box...");

  const boxSelector = document.getElementById("boxSelector");
  if (!boxSelector) {
    console.error("Elemento #boxSelector no encontrado.");
    return;
  }

  for (let i = 1; i <= 12; i++) {
    const btn = document.createElement("button");
    btn.textContent = `Box ${i}`;
    btn.onclick = () => selectBox(i);
    boxSelector.appendChild(btn);
  }

  console.log("Botones Box generados.");

  // Deshabilitar campos inicialmente
  deshabilitarCampos();

  // Verificar sesión
  try {
    const r = await fetch("check_session.php", { credentials: 'same-origin' });
    const d = await r.json();

    if (!d.authenticated || !d.user_id) {
      throw new Error("Sesión inválida o user_id faltante");
    }

    currentUserId = d.user_id;
    sessionStorage.setItem('currentUserId', currentUserId);
    document.getElementById("contenidoApp").style.display = "block";
    cargarListadoInformesGuardados();
  } catch (error) {
    console.error("Error al verificar la sesión:", error);
    sessionStorage.removeItem("currentUserId");
    window.location.href = "index.html";
  }
});

// Resto de funciones...




//===========================================================

// Definición de cerrarSesion
// ====== Función Cerrar Sesión ======
  function cerrarSesion() {
    if (currentUserId) {
      Object.keys(sessionStorage)
        .filter(k => k.startsWith(`autosave_${currentUserId}_`))
        .forEach(k => sessionStorage.removeItem(k));
    }
    sessionStorage.removeItem("currentUserId");
    window.location.href = "index.html";
  }
  
// Resto de funciones...

function saveLocal(boxNumber) {
  if (!boxNumber || !currentUserId) return;
  const datos = {};
  campos.forEach(id => {
    datos[id] = document.getElementById(id)?.value || '';
  });
  sessionStorage.setItem(`autosave_${currentUserId}_box${boxNumber}`, JSON.stringify(datos));
}

 function loadLocal(boxNumber) {
  if (!boxNumber || !currentUserId) return null;
  const str = sessionStorage.getItem(`autosave_${currentUserId}_box${boxNumber}`);
  try {
    return str ? JSON.parse(str) : null;
  } catch {
    return null;
  }
}

async function saveDraft() {
  if (!selectedBox || !currentUserId) {
    console.warn("No hay usuario o box seleccionado");
    return;
  }
  if (!selectedBox || !currentUserId) return;
  const datos = {};
  campos.forEach(id => {
    datos[id] = document.getElementById(id).value;
  });
  console.log("Guardando draft:", { box: selectedBox, datos }); // 👈 Aquí
  await fetch('save_draft.php', {
    method: 'POST',
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ box: selectedBox, datos })
  });
}


function attachAutosaveListeners() {
  campos.forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('input', () => {
      actualizarContadorTotal();
      clearTimeout(autosaveTimer);
      autosaveTimer = setTimeout(() => {
        saveDraft(); // Solo guardamos como draft
      }, DEBOUNCE_MS);
    });
  });
}




    /* -----------------------------------------------
     *  Suma los caracteres que hay en TODOS los campos
     *  y pinta el resultado en el <span id="contador-total">
     * ---------------------------------------------*/
    function actualizarContadorTotal() {
      let total = 0;
      campos.forEach(id => {
        const el = document.getElementById(id);
        if (el) total += el.value.length;
      });
      document.getElementById('contador-total').textContent = total;
      const LIMITE = 1200;
      const spanTotal = document.getElementById('contador-total');
      if (total > LIMITE) {
        spanTotal.classList.add('contador-alerta');
      } else {
        spanTotal.classList.remove('contador-alerta');
      }

    }



function habilitarCampos() {
  campos.forEach(campoId => {
    const textarea = document.getElementById(campoId);
    if (textarea) {
      textarea.disabled = false;
      textarea.placeholder = "";
    } else {
      console.warn(`Elemento con ID ${campoId} no encontrado.`);
    }
  });
}

function deshabilitarCampos() {
  campos.forEach(campoId => {
    const textarea = document.getElementById(campoId);
    if (textarea) {
      textarea.disabled = true;
      textarea.placeholder = "Seleccione un Box o informe guardado";
    }
  });
}


    
    
    function borrarDatos() {
  if (!currentUserId) {
    console.error("currentUserId no está definido. No se pueden borrar los drafts.");
    return;
  }

  campos.forEach(c => {
    const el = document.getElementById(c);
    if (el) el.value = '';
  });
}

  // Eliminar datos locales
  if (selectedBox && currentUserId) {
    sessionStorage.removeItem(`autosave_${currentUserId}_box${selectedBox}`);
  }

  // Eliminar datos del servidor
  console.log("Eliminando draft del box:", selectedBox);
fetch('clear_draft.php', {
  method: 'POST',
  credentials: 'same-origin',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ box: selectedBox })
})
.then(res => res.json())
.then(data => {
  console.log("Respuesta del servidor al eliminar draft:", data);
  if (!data.success) {
    console.error("Error al eliminar draft:", data.message);
  }
})
.catch(err => {
  console.error("Error en la petición al servidor:", err);
});


    function copiarInforme() {
      const el = document.getElementById('resultado');
      const html = el.innerHTML;
      const txt = el.innerText;               // respaldo texto plano

      /* 1- Intenta el nuevo API (Edge/Chrome 79+, Firefox ≥ 111) */
      try {
        const item = new ClipboardItem({
          'text/html': new Blob([html], { type: 'text/html' }),
          'text/plain': new Blob([txt], { type: 'text/plain' })
        });
        return navigator.clipboard.write([item])
          .then(() => alert('Informe copiado con formato.'))
          .catch(() => fallback());
      } catch (_) {
        fallback();                            // Safari o navegadores antiguos
      }

      /* 2- Fallback clásico: crea <textarea>, execCommand('copy') */
      function fallback() {
        const ta = document.createElement('textarea');
        ta.value = html;                       // sí, incluye etiquetas
        ta.style.position = 'fixed';           // no salta la pantalla
        ta.style.opacity = 0;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        alert('Informe copiado (sin formato enriquecido)');  // aviso “plano”
      }
    }



  function generarTextoDesdeDatos(datos) {
  const h = new Date().getHours();
  const turno = (h >= 8 && h < 20) ? 'Turno de 8 a 20 horas' : 'Turno de 20 a 8 horas';
  
  let html = `<p><strong>BOX ${selectedBox} – ${turno}</strong></p>`;
  
  campos.forEach(id => {
    const etiqueta = document.querySelector(`label[for='${id}']`).innerText;
    const valor = datos[id]?.trim() || 'Sin especificar';
    html += `<p><strong>${etiqueta}:</strong> ${valor}</p>`;
  });

  return html;
}



function clonarInforme() {
  currentInformeId = null;    // así forzamos nuevo UUID
  generarInforme();
}



    // 2) Generar o actualizar un informe
    
 async function generarInforme() {
  if (!selectedBox || !currentUserId) {
    return alert("Selecciona un Box y asegúrate de estar autenticado.");
  }

  // Siempre forzamos un INSERT nuevo
  currentInformeId = crypto.randomUUID();

  // Recojo los valores de los textarea
  const datos = {};
  campos.forEach(c => {
    datos[c] = document.getElementById(c).value.trim() || '';
  });

  // Previsualización
  const html = generarTextoDesdeDatos(datos);
  const divRes = document.getElementById('resultado');
  divRes.innerHTML = html;
  divRes.style.display = 'block';
  document.getElementById('copiarInformeBtn').style.display = 'inline-block';

  try {
    // Guardar en la tabla "reports"
    const res = await fetch("save_report.php", {
      method: "POST",
      credentials: 'same-origin',
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id: currentInformeId,
        user_id: currentUserId,
        box: selectedBox,
        datos,
        timestamp: new Date().toISOString()
      })
    });
    const d = await res.json();
    if (d.success) {
      // Borramos el draft de esta caja
      await fetch('clear_draft.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ box: selectedBox })
      });
      // Actualizamos el ID por si el servidor lo ha cambiado
      currentInformeId = d.id || currentInformeId;
      alert("Informe generado/actualizado con éxito.");
      cargarListadoInformesGuardados();
    } else {
      console.warn("Error al guardar informe:", d.message);
      alert("Error guardando el informe.");
    }
  } catch (err) {
    console.error("Error en generarInforme():", err);
    alert("No se pudo conectar con el servidor.");
  }
}
   
   
   
//-------------------------------------------
// En la función selectBox():
async function selectBox(boxNumber) {
  if (selectedBox) saveLocal(selectedBox); // Guardar el box anterior si está definido
  selectedBox = boxNumber;

  // Marcar botón activo
  document.querySelectorAll(".box-selector button").forEach(button => button.classList.remove("active"));
  document.querySelector(`.box-selector button:nth-child(${boxNumber})`).classList.add("active");

  // Mostrar mensaje de selección
  document.getElementById("numero-box-seleccionado-msg").textContent = boxNumber;
  document.getElementById("mensaje-box-seleccionado").style.display = "block";

  // Habilitar campos
  habilitarCampos();

  // Cargar borrador o último informe guardado
  try {
    const res = await fetch(`get_draft.php?box=${boxNumber}`, { credentials: 'same-origin' });
    const js = await res.json();
    if (js.success && js.datos) {
      currentInformeId = null; // Forzar nuevo informe
      campos.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
          element.value = js.datos[id] || '';
        } else {
          console.warn(`Elemento con ID ${id} no encontrado.`);
        }
      });
      actualizarContadorTotal();
      return;
    }
  } catch (e) {
    console.error('Error al cargar borrador:', e);
  }

  // Si no hay borrador, intentar cargar último informe
  try {
    const res = await fetch(`get_latest_report.php?box=${boxNumber}`, { credentials: 'same-origin' });
    const data = await res.json();
    if (data.success) {
      currentInformeId = data.id;
      currentInformeBox = boxNumber;
      campos.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
          element.value = data.datos[id] || '';
        } else {
          console.warn(`Elemento con ID ${id} no encontrado.`);
        }
      });
      actualizarContadorTotal();
      return;
    }
  } catch (e) {
    console.error("No pudo cargar último informe:", e);
  }

  // Si no hay borrador ni informe, limpiar datos
  currentInformeId = null;
  borrarDatos();
}

//-------------------------------------------
    async function guardarInformeAuto() {
  if (!selectedBox || !currentUserId) return;

  // ID para este informe (nueva o existente)
  if (!currentInformeId) {
    currentInformeId = crypto.randomUUID();
    currentInformeBox = selectedBox;
  }

  // recogemos datos
  const datos = {};
  campos.forEach(c => datos[c] = document.getElementById(c).value.trim());

  // enviamos
  try {
    const res = await fetch("save_report.php", {
      method: "POST",
      credentials: "same-origin",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id:        currentInformeId,
        user_id:   currentUserId,
        box:       currentInformeBox,
        datos,
        timestamp: new Date().toISOString()
      })
    });
    const json = await res.json();
    if (json.success) {
      // si el servidor cambia el ID (por ejemplo tras INSERT), lo actualizamos
      currentInformeId = json.id || currentInformeId;
      console.info("Autosave OK:", currentInformeId);
    } else {
      console.warn("Autosave falló:", json.message);
    }
  } catch (err) {
    console.error("Error en autosave:", err);
  }
}




function saveReport() {
  // 1) Si no hay Box seleccionado o user, nada
  if (!selectedBox || !currentUserId) return;

  // 2) Construye el objeto datos
  const datos = {};
  campos.forEach(id => {
    const ta = document.getElementById(id);
    if (ta) datos[id] = ta.value.trim();
  });

  // 3) Prepara el payload
  if (!currentUserId || !selectedBox) {
  console.error("Faltan datos necesarios para guardar el informe.");
  return;
}
  const payload = {
    id:        currentInformeId,        // puede ser null la primera vez
    user_id:   currentUserId,
    box:       selectedBox,
    datos:     datos,
    timestamp: new Date().toISOString()
  };

  // 4) Envío al servidor
  fetch('save_report.php', {
  method: 'POST',
  credentials: 'same-origin',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify(payload)
})
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        // si el servidor devolvió un id nuevo, guárdalo
        currentInformeId = d.id || currentInformeId;
        console.info('Autosave ok:', currentInformeId);
      } else {
        console.warn('Autosave fallo:', d.message);
      }
    })
    .catch(err => {
      console.error('Error autosave:', err);
    });
}




// 1) Listar todos los informes guardados en el <select>
async function cargarListadoInformesGuardados() {
  try {
    const res = await fetch('list_reports.php', {
      credentials: 'same-origin'
    });
    const data = await res.json();
    console.log("Respuesta de list_reports.php:", data); // DEBUG
    if (Array.isArray(data.reports)) {
      const select = document.getElementById('informesGuardados');
      select.innerHTML = '<option value="">Seleccione un informe...</option>';
      data.reports.forEach(informe => {
        const option = document.createElement('option');
        option.value = informe.id;
        option.textContent = `[Box ${informe.box}] ${new Date(informe.fecha).toLocaleString()} – ${informe.hora}`;
        select.appendChild(option);
      });
    } else {
      console.warn("La respuesta de list_reports.php no es un array:", data);
    }
  } catch (e) {
    console.error("Error al cargar listado:", e);
  }
}


async function cargarInformeDesdeLista(sel) {
  const id = sel.value;
  if (!id) {
    borrarDatos();
    return;
  }
  try {
    const res = await fetch(`get_report.php?id=${encodeURIComponent(id)}`, {
      credentials: 'same-origin'
    });
    const json = await res.json();
    if (!json.success) {
      return alert(json.message || "Informe no encontrado");
    }

    // Rellenar campos
    currentInformeId = json.id;
    selectedBox = json.box;
    habilitarCampos();
    campos.forEach(c => {
      document.getElementById(c).value = json.datos[c] || "";
    });
    actualizarContadorTotal();

    // Marcar botón de box activo
    document.querySelectorAll(".box-selector button").forEach(b => b.classList.remove("active"));
    document.querySelector(`.box-selector button:nth-child(${json.box})`).classList.add("active");

    // Previsualización
    const html = generarHTMLDesdeDatos(json.datos);
    const div = document.getElementById("resultado");
    div.innerHTML = html;
    div.style.display = "block";
    document.getElementById("copiarInformeBtn").style.display = "inline-block";

    // Guardar como draft
    await saveDraft();
  } catch (err) {
    console.error("Error en fetch get_report.php:", err);
    alert("Error de conexión o servidor.");
  }
}




async function loadReport(id) {
  try {
    const response = await fetch(`get_report.php?id=${id}`);
    const data = await response.json();

    console.log("Datos recibidos:", data); // Verifica la respuesta

    if (data.success) {
      habilitarCampos();
      const datos = data.data;

      campos.forEach(campoId => {
        const textarea = document.getElementById(campoId);
        if (textarea) {
          textarea.value = datos[campoId] || '';
        } else {
          console.error(`El textarea con ID ${campoId} no existe.`);
        }
      });

      // Actualizar contador y otros estados
      actualizarContadorTotal();
      selectedBox = data.box;
      currentInformeId = id;
      currentInformeBox = data.box;

      // Actualizar estado visual
      document.querySelectorAll('.box-selector button').forEach(b => b.classList.remove('active'));
      document.querySelector(`.box-selector button:nth-child(${data.box})`).classList.add('active');
      document.getElementById('numero-box-seleccionado-msg').textContent = data.box;
      document.getElementById('mensaje-box-seleccionado').style.display = 'block';
      document.getElementById('resultado').innerHTML = generarHTMLDesdeDatos(datos);
      document.getElementById('resultado').style.display = 'block';
      document.getElementById('copiarInformeBtn').style.display = 'inline-block';
    } else {
      alert(data.message);
    }
  } catch (err) {
    console.error("Error al cargar el informe:", err);
    alert("No se pudo conectar con el servidor.");
  }
}



    
    
    function generarHTMLDesdeDatos(datos) {
  const h = new Date().getHours();
  const turno = (h >= 8 && h < 20) ? 'Turno de 8 a 20 horas' : 'Turno de 20 a 8 horas';
  
  let html = `<p class="cabecera">BOX ${selectedBox} – ${turno}</p>`;
  
  campos.forEach(campoId => {
    const etiqueta = document.querySelector(`label[for='${campoId}']`).innerText;
    const valor = datos[campoId]?.trim() || '<span class="no-especificado">Sin especificar</span>';
    html += `<p><span class="label-strong">${etiqueta}:</span> ${valor}</p>`;
  });

  return html;
}
    // fin de generarHTMLDesdeDatos(datos) 





    function imprimirAuto() {
      imprimir(false);
    }

    function imprimirAlternativo() {
      imprimir(true);
    }



    function prepararYImprimir(esTurnoDiurno) {
      const resultado = document.getElementById("resultado");
      resultado.classList.remove("diurno-print", "nocturno-print");
      resultado.classList.add(esTurnoDiurno ? "diurno-print" : "nocturno-print");
      resultado.style.display = "block";
      window.print();
    }


// Para eliminar un informe concreto
async function eliminarInforme() {
  if (!currentInformeId) {
    return alert("No hay informe seleccionado");
  }
  if (!confirm("¿Eliminar este informe?")) return;

  try {
    const res = await fetch("delete_reports.php", {
      method: "POST",
      credentials: "same-origin",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: currentInformeId })
    });
    const json = await res.json();
    if (json.success) {
      alert(json.message || "Informe eliminado");
      currentInformeId = null;
      borrarDatos();
      cargarListadoInformesGuardados();
    } else {
      alert("Error al eliminar: " + json.message);
    }
  } catch (e) {
    console.error(e);
    alert("No se pudo conectar con el servidor.");
  }
}

// Para eliminar todos los informes de un box
async function eliminarInformesDeBox() {
  if (!selectedBox) {
    return alert("Selecciona primero un Box.");
  }
  if (!confirm(`¿Eliminar todos los informes de Box ${selectedBox}?`)) return;

  try {
    const res = await fetch("delete_box_reports.php", {
      method: "POST",
      credentials: "same-origin",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ box: selectedBox })
    });
    const json = await res.json();
    if (json.success) {
      alert(json.message || "Informes del box eliminados");
      currentInformeId = null;
      borrarDatos();
      cargarListadoInformesGuardados();
    } else {
      alert("Error al eliminar informes del box: " + json.message);
    }
  } catch (e) {
    console.error(e);
    alert("No se pudo conectar con el servidor.");
  }
}




//actualiza su posición al hacer scroll:

function actualizarPosicionBoxIndicador() {
  const indicador = document.getElementById("box-indicador-flotante");
  if (!indicador) return;

  const offset = 20;
  const scrollTop = window.scrollY || document.documentElement.scrollTop;
  indicador.style.top = `${scrollTop + offset}px`;

}

// Ejecutar al cargar y al hacer scroll
window.addEventListener("scroll", actualizarPosicionBoxIndicador);
window.addEventListener("resize", actualizarPosicionBoxIndicador);



function imprimir(turnoAlternativo = false) {
  const horaActual = new Date().getHours();
  const esDiurno = (horaActual >= 8 && horaActual < 20);
  const usarTurnoDiurno = turnoAlternativo ? !esDiurno : esDiurno;
  
  // Generamos el HTML con el estilo compacto
  const datos = {};
  campos.forEach(campoId => {
    datos[campoId] = document.getElementById(campoId).value.trim() || "Sin especificar";
  });

  let html = `<p class="imprimir-cabecera">BOX ${selectedBox} – ${usarTurnoDiurno ? "Turno de 8 a 20 horas" : "Turno de 20 a 8 horas"}</p>`;
  
  campos.forEach(campoId => {
    const etiqueta = document.querySelector(`label[for='${campoId}']`).innerText;
    const valor = datos[campoId];
    html += `<p class="imprimir-parrafo"><span class="imprimir-label-strong">${etiqueta}:</span> <span class="imprimir-texto-normal">${valor}</span></p>`;
  });

  const divResultado = document.getElementById("resultado");
  divResultado.innerHTML = html;
  
  // Aplicamos la clase correspondiente al turno
  divResultado.classList.remove("diurno-print", "nocturno-print");
  divResultado.classList.add(usarTurnoDiurno ? "diurno-print" : "nocturno-print");
  
  divResultado.style.display = "block";
  window.print();
}

        </script>
    </body>
</html>