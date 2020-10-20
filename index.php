<?php
    header('Content-Type: text/html; charset=utf-8');

    function myErrorHandler($errno, $errstr, $errfile, $errline){}
    set_error_handler("myErrorHandler");

    require_once "vendor/autoload.php";

    $planilhas = array();

//    $fileName = "Planejamento Financeiro.xlsx";
    $fileName = "FÁBRICA CAIXINHA.xlsx";
//    $fileName = "Pasta1.xlsx";

    /** detecta automaticamente o tipo de arruivo que será carregado */
    $excelReader = PHPExcel_IOFactory::createReaderForFile($fileName);

    /** Definindo manualmente.
    // $inputFileType = 'Excel5';
    // $inputFileType = 'Excel2007';
    // $inputFileType = 'Excel2003XML';
    // $inputFileType = 'OOCalc';
    // $inputFileType = 'SYLK';
    // $inputFileType = 'Gnumeric';
    // $inputFileType = 'CSV';
    $excelReader = PHPExcel_IOFactory::createReader($inputFileType);
     */

//    Se não precisarmos de formatação
    $excelReader->setReadDataOnly();

//    carregar apenas algumas abas
//    $loadSheets = array('COTAS');
//    $loadSheets = array('EMPRÉST. FÁBRICA');
//    $excelReader->setLoadSheetsOnly($loadSheets);

//    o comportamente padrão é carregar todas as abas
    $excelReader->setLoadAllSheets();

    $excelObj = $excelReader->load($fileName);

    $excelObj->getActiveSheet()->toArray(null, true,true,true);

    //Pega os nomes das abas
    $worksheetNames = $excelObj->getSheetNames($fileName);
    $return = array();
    foreach($worksheetNames as $key => $sheetName)
    {
        //define a aba ativa
        $excelObj->setActiveSheetIndexByName($sheetName);

        //cria um array com o nome da aba como índice
        $return[$sheetName] = $excelObj->getActiveSheet()->toArray(null, true,true,true);
    }
    echo "<pre>";
    foreach ($return as $key => $value)
    {
        $sizePlanilha = sizeof($value);
        for ($linha = 1; $linha <= $sizePlanilha; $linha++)
        {
            $contadorColunaVazia = 0;
            foreach ($value[$linha] as $coluna)
            {
                if (empty($coluna))
                    $contadorColunaVazia++;
            }
            if (sizeof($value[$linha]) == $contadorColunaVazia)
            {
                unset($value[$linha]);
            }

        }
        $linhasEColunas = array();
        $nomeDasColunas = array();
        $linhas = array();

        for ($linha = 1, $linhaAUX = 0; $linha <= $sizePlanilha ; $linha++, $linhaAUX++)
        {
            if (sizeof($value[$linha]) == 0)
            {
                $linhaAUX--;
                continue;
            }
            foreach ($value[$linha] as $coluna)
            {
                $linhasEColunas[$linhaAUX][] = $coluna;
            }
        }

        $nomeDasColunas = $linhasEColunas[0];
        for($linha = 1; $linha < sizeof($linhasEColunas); $linha++)
            $linhas[] = $linhasEColunas[$linha];
        unset($linhasEColunas);

        $planilhas[$key] = array("coluna" => $nomeDasColunas, "linha" => $linhas);
//        echo $key." - ";
//        echo sizeof($planilhas)."<br>";
    }
//    echo "<br><hr><br>";






    foreach ($planilhas as $key => $planilha)
    {
?>
<h2><?php echo $key; ?></h2>]
<style>
    table tr td, table tr th
    {
        text-align: center;
        border: 1px solid silver;
    }
</style>
<table>
    <tr>
        <?php
            for($coluna = 0; $coluna < sizeof($planilha["coluna"]); $coluna++)
            {
        ?>
        <th>
        <?php
            echo $planilha["coluna"][$coluna];
        ?>
        </th>
        <?php
            }
        ?>
    </tr>
    <?php
        for($linha = 0; $linha < sizeof($planilha["linha"]); $linha++)
        {
            echo "<tr>";
            for($coluna = 0; $coluna < sizeof($planilha["linha"][$linha]); $coluna++)
            {
    ?>
        <td>
            <?php
                echo $planilha["linha"][$linha][$coluna];
            ?>
        </td>
    <?php
            }
            echo "</tr>";
        }
    ?>
</table>
<?php
    }
//    print_r($planilhas);
?>