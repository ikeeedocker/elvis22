<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="vue.js"></script>
</head>
<body style="background-color: #000000;color:#FFFFFF;">
    




<?php 
$page = $_GET['page'];

$content = file_get_contents($page);
libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML($content);

$parsed = element_to_obj($dom->documentElement);

$exp_page = explode('/', $page);
$base_url = $exp_page[0]."/".$exp_page[1]."/".$exp_page[2];
?>
<?php
$vueObj = [];
foreach($parsed['children'][2]['children'][1]['children'][1]['children'][2]['children'] as $k => $elem){
    if(isset($elem['class']) && $elem['class'] == 'col-md-2'){
        $imageToParse = $elem['children'][0]['children'];
        $src = str_replace('..', '', $imageToParse[0]['children'][0]['src']);
        $text = $imageToParse[1]['children'][0]['children'][0]['html'];
        $vueObj[] = [
            'url' => $base_url.$src,
            'text' => $text,
            'selected' => 0,
        ];
    }
}
?>
<?php

function element_to_obj($element){
    $obj = [];
    if($element->nodeType == XML_ELEMENT_NODE){
        $obj = array( "tag" => $element->tagName );
        if(isset($element->attributes)){
            foreach ($element->attributes as $attribute) {
                $obj[$attribute->name] = $attribute->value;
            }
            foreach ($element->childNodes as $subElement) {
                if ($subElement->nodeType == XML_TEXT_NODE) {
                    $obj["html"] = $subElement->wholeText;
                }
                else {
                    $obj["children"][] = element_to_obj($subElement);
                }
            }
        }
    }
    return $obj;
}
?>


<style>
    .selected{
        background-color: #dbdbdb;
        color: #000000;
        font-weight: bold;
    }
    input{
        padding:5px;
        width: 100%;
    }
</style>
<div id="app">
    <table>
        <thead>
            <th style="width: 5%;">Selezionato</th>
            <th style="width: 5%;">id</th>
            <th style="width: 35%;">img</th>
            <th style="width: 35%;">src</th>
            <th style="width: 30%;">text</th>
        </thead>
        <tbody>
            <tr v-for="(elem, k) in data"
                v-bind:class="{ 'selected': elem.selected }"
            >
                <td style="text-align: center;"
                    @click="elem.selected = !elem.selected">
                    <template v-if="elem.selected">SI</template>
                    <template v-if="!elem.selected">NO</template>
                </td>
                <td style="text-align: center;"
                    @click="elem.selected = !elem.selected">{{ k }}</td>
                <td style="text-align: left;"
                    @click="elem.selected = !elem.selected">{{ elem.url }}</td>
                <td style="text-align: center;"
                    @click="elem.selected = !elem.selected"><img :src="elem.url" :alt="elem.text"></td>
                <td style="text-align: center">
                    <input type="text" v-model="elem.text">
                </td>
            </tr>
        </tbody>
    </table>
    <div style="text-align: center">
        <button @click="checkCSV()">Termina</button>
    </div>
    <div v-if="selectedImages.length > 0">
        <table>
            <thead>
                <th style="width: 5%;">Selezionato</th>
                <th style="width: 5%;">id</th>
                <th style="width: 35%;">img</th>
                <th style="width: 35%;">src</th>
                <th style="width: 30%;">text</th>
            </thead>
            <tbody>
                <tr v-for="(elem, k) in selectedImages" class="selected">
                    <td style="text-align: center;">
                        <button @click="selectedImages.splice(k, 1)">Deseleziona</button>
                    </td>
                    <td style="text-align: center;">{{ k }}</td>
                    <td style="text-align: left;">{{ elem.url }}</td>
                    <td style="text-align: center;"><img :src="elem.url" :alt="elem.text"></td>
                    <td style="text-align: center">
                        <input type="text" v-model="elem.text">
                    </td>
                </tr>
            </tbody>
        </table>
        <div style="text-align: center">
            <button @click="generaCsv()">Genera CSV</button>
        </div>
    </div>
</div>
<script>
  var app = new Vue({
    el: '#app', //indicare il selettore al contenitore della app
    data () {
      return {
        data: <?php echo json_encode($vueObj); ?>, //struttura dati 
        selectedImages: [],
      }
    },
    mounted () {
        //chiamata ajax per recupero dati
      
    },
    filters: {
        // sezione per indicare i metodi di filtro, opzionale
    },
    methods: {
        checkCSV(){
            this.selectedImages = this.data.filter(function (el) {
                return el.selected;
            })
            .sort(function (a, b) {
                return a.text > b.text;
            });
        },
        generaCsv(){
            csvSelectedImages = "data:text/csv;charset=utf-8," + this.selectedImages.map(function (el){
                return '"' + el.text + '";"' + el.url + '"';
            })
            .join('\n\r');
            console.log(csvSelectedImages);
            var encodedUri = encodeURI(csvSelectedImages);
            //window.open(encodedUri);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "<?php echo str_replace("/", "_", str_replace(":", "", str_replace(".", "_", $page))); ?>.csv");
            document.body.appendChild(link);
            link.click();
        }
    },
    components: {
        //aggiunta vue components prefabbricati
    }
  });
</script>


</body>
</html>