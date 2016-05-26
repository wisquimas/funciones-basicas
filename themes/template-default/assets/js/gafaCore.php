<?php
/*
** DEFINIMOS VARIABLES GLOBALES
*/
?><script type="text/javascript">
    gafacore = {};

    /**
     * Url to the wordpress' template.
     * @type {string}
     */
    gafacore.plantilla = "<?php plantilla();?>";

    /**
     * Contains the PHP GET variables in JS.
     * @type {object}
     */
    gafacore._GET = {};
    <?php
    $getVarName = "gafacore._GET";
    $js = ";$getVarName={};";
    foreach($_GET as $varName => $varValue) {
        // Si es un objeto complejo, hay que codificar en json.
        if(is_array($varValue) || is_object($varValue)) {
            $jsonValue = json_encode($varValue);
            $js .= "$getVarName.$varName=$jsonValue;";
        } else {
            $js .= "$getVarName.$varName=\"$varValue\";";
        }
    }
    echo $js;
    ?>

    /**
     * Sube archivos por ajax
     * @param files string, array de objetos File. Aunque tambien, es un array asociativo, cuyo value puede ser cualquier otra cosa.
     * @param processFile string, nombre del archivo en la carpeta "procesos" que hara el handle.
     * @return jqXHR regresa el objeto jsXHR del request ajax.
     */
     gafacore.AjaxUpload = function(files, processFile) {
        // Create a formdata object and add the files
        var formData = new FormData();

        for(var fileIndex in files) {
            if(!files.hasOwnProperty(fileIndex)) continue;
            formData.append(fileIndex, files[fileIndex]);
        }

        return $.ajax({
            url: '<?php echo plantilla(false) . "/procesos/"; ?>' + processFile,
            type: 'POST',
            data: formData,
            cache: false,
            dataType: 'json',
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
        });
    };

    /**
     * Makes an ajax post request.
     *
     * @param data object. Data to send.
     *      If the param 'processFile' is provided, then, you MUST provide in this param, the atributes:
     *          'funcion' : string Name of the function in the do_action.php that will handle the request.
     *          'attr' : object, params to pass the the function to call in the do_action.php.
     * @param processFile string. (optional) Name of the file in the directory "procesos" that will do the handle of the request.
     *          If it's not provided, the file do_action.php will handle the request, and the 'data' parameter must be set as
     *          described above.
     * @return jqXHR return the jsXHR object of the ajax request.
     */
     gafacore.post = function(data, processFile) {
          var url = !!processFile ?
              "<?php echo plantilla(false) . "/procesos/"; ?>" + processFile :
              "<?php echo plantilla(false) . "/procesos/do_action.php"; ?>";

          return $.ajax({
              url: url,
              type: 'POST',
              data: data,
              cache: false,
              dataType: 'json',
          });
    };

    /**
     * Muestra un fancy identificado por un id.
     * @param fancyId string id del fancy como se asigno en el atributo "data-fancy" del div del fancy.
     * @param onClose function (opcional) uncion a ser llamada cuando se cierre el fancy.
     */
    gafacore.ShowFancy = function(fancyId, onClose) {
        return $("[data-fancy='"+fancyId+"']").show().data("onClose", onClose);
    };

    /**
     * Regresa una fecha en formato "yyyy-MM-dd".
     * @return string
     */
    gafacore.GetValidDate = function(date) {
        var year 		= parseInt(date.year);
        if (year<999 || isNaN(year))	year = 1970;

        var month 		= parseInt(date.month);
        if(month<1 || month>12 || isNaN(month)) month = 1;
        if(month<=9) month = "0" + month;

        var day 		= parseInt(date.day);
        if(day<1 || day>31 || isNaN(day)) day = 1;
        if(day<=9) day = "0" + day;

        return year + "-" + month + "-" + day;
    };

</script>
