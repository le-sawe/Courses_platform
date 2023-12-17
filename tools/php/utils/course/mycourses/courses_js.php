<script>
    // CHECK FILE SIZE
    function Filevalidation(id) {
        const fi = document.getElementById(id);
        // Check if any file is selected.
        if (fi.files.length > 0) {
            for (var i = 0; i <= fi.files.length - 1; i++) {
                const fsize = fi.files.item(i).size;
                const file = Math.round((fsize / 1024));
                // The size of the file.
                if (fsize >= <?php echo $para_file_max_size ?>) {
                    alert(
                    "File too large, please select a file less than 40mb");
                } else {
                    console.log( '<b>' + Math.round((fsize / 1024)) + '</b> KB');
                }
            }
        }
    }
    // mdb initialization
    $(document).ready(function() {
        $('.mdb-select').materialSelect();
    });
    
    $(function () {// Tooltips Initialization
        $('[data-toggle="tooltip"]').tooltip()
    })         
    
    <?php  // Get all sub material and stock them on javascript array        
        echo "var all_sub_materials = JSON.parse('". json_encode($all_sub_materials)."');\n";
    ?>

    //file section
        file_indexing =0 ;// initialize
        file_count =0 ;// initialize
        function add_file_input(){// add a file input
            if(file_count < <?php echo $file_limite; ?>){
                file_indexing ++;
                file_count ++;
                // CREATE FILE
                    //Get The File that contain all the upload field
                    var all_files_input_div =document.getElementById("files_input_div"); // get the div that should contain the file inputs
                    
                    // Create the row div
                    var row_div = document.createElement("div");
                    row_div.className ="row w-100 flex-wrap";
                    row_div.id ="div_"+file_indexing;
                    all_files_input_div.appendChild(row_div);

                    // Create the file field div
                    var files_input_div = document.createElement("div");
                    files_input_div.className ="file-field col";
                    row_div.appendChild(files_input_div);

                    // Create the file field div
                    var remove_button_div = document.createElement("div");
                    remove_button_div.className ="file-field col";
                    row_div.appendChild(remove_button_div);

                    // Create the file button div
                    var file_button_div = document.createElement("div");
                    file_button_div.className = "btn btn-black  mb-2 float-left";
                    files_input_div.appendChild(file_button_div);

                    // Create the span
                    var span = document.createElement("span");
                    
                    file_button_div.appendChild(span);

                    // Create Icon
                    var icon = document.createElement("i");
                    icon.id="icon_"+file_indexing+"";
                    icon.className = "fas fa-upload mr-2";
                    span.appendChild(icon);
                    document.getElementById("icon_"+file_indexing+"").setAttribute("aria-hidden", "true") ;

                    span.innerHTML +="Choose file";

                    // Create the file path div
                    var path_div = document.createElement("div");
                    path_div.className = "file-path-wrapper";
                    files_input_div.appendChild(path_div);
                
                    // Create the text input div
                    var path_input = document.createElement("input");
                    path_input.type="text";
                    path_input.placeholder="Upload your file";
                    path_input.className = "file-path validate mt-1";
                    path_div.appendChild(path_input);

                    // Create input
                    var input = document.createElement("input"); // create  input element
                    //add detail to the elemnet
                    input.type = "file";
                    input.id = "file_"+file_indexing+"";
                    input.label = "file_"+file_indexing+"";
                    input.name = "file[]"; 
                    input.setAttribute("required","");
                    input.setAttribute("onchange","Filevalidation('file_"+file_indexing+"')");
                    file_button_div.appendChild(input); // put the inputinto the div

                    // Create remove button
                    var remove_button =document.createElement("button");
                    remove_button.className="btn btn-black btn-rounded col  ";
                    remove_button.type="button";
                    remove_button.id="remove_button_file_"+file_indexing+"";
                    remove_button.setAttribute("onclick", 'remove_file_input('+file_indexing+')');
                    remove_button.innerHTML='<i class="fas fa-trash"></i>';
                    remove_button_div.appendChild(remove_button);
                }else{alert("You can't add more than 10 files to the course")}
        }
        function remove_file_input(element_id){// remove a file input
            file_count --;
            document.getElementById("div_"+element_id).remove(); // remove element
        }
    //keyword section
        keyword_count =<?php echo $keyword_count ?> ;// initialize
        function add_keyword_input(){// add a keyword input
            keyword_count ++;
            var all_keywords_input_div =document.getElementById("keywords_input_div"); // get the div that should contain the keyword inputs
            
            // Create the row div
            var row_div = document.createElement("div");
            row_div.className ="row w-100";
            row_div.id ="key_div_"+keyword_count;
            all_keywords_input_div.appendChild(row_div);

            // Create the keyword field div
            var keywords_input_div = document.createElement("div");
            keywords_input_div.className =" col md-form";
            row_div.appendChild(keywords_input_div);

            // Create the keyword field div
            var remove_button_keyword_div = document.createElement("div");
            remove_button_keyword_div.className =" col";
            row_div.appendChild(remove_button_keyword_div);

            //Create prefix icon
            var prefix_icon = document.createElement("i");
            //add detail
            prefix_icon.className="fas mr-2 fa-hashtag prefix";
            keywords_input_div.appendChild(prefix_icon);

            //Create input
            var input = document.createElement("input"); // create  input element
            //add detail to the elemnet
            input.type = "text";
            input.id = "keyword_"+keyword_count+"";
            input.label = "keyword_"+keyword_count+"";
            input.name = "keyword[]"; 
            input.setAttribute("list","keywords_list");
            input.setAttribute("oninput","keyword_options('keyword_"+keyword_count+"')");
            input.setAttribute("required","");
            input.setAttribute("minlength","<?php echo $para_keyword_min_length?>");
            input.setAttribute("maxlenght","<?php echo $para_keyword_max_length?>");
            input.className = "form-control-sm mdb-autocomplete"; 
            keywords_input_div.appendChild(input); // put the inputinto the div

            //Create Label
            var label = document.createElement("label");
            //add detail
            label.innerHTML="   Keyword";
            label.setAttribute("for", 'keyword_'+keyword_count);
            keywords_input_div.appendChild(label);

            // Create remove button
            var remove_button =document.createElement("button");
            remove_button.className="btn btn-black col btn-rounded ";
            remove_button.type="button";
            remove_button.id="remove_button_keyword_"+keyword_count+"";
            remove_button.setAttribute("onclick", 'remove_keyword_input('+keyword_count+')');
            remove_button.innerHTML='<i class="fas fa-trash"></i>';
            remove_button_keyword_div.appendChild(remove_button);

            document.getElementById("keyword_count").value = keyword_count;

        }
        function remove_keyword_input(element_id){// remove a keyword input
            document.getElementById("key_div_"+element_id).remove(); // remove element
            document.getElementById("keyword_count").value = keyword_count;
        } 
        function keyword_options(id){
            input = document.getElementById(id);
            if (input.value.length > 1) {
                    $.ajax({
                    type: "GET",
                    url: "<?php echo $base_url ?>course/tools/keyword/keyword_engine.php",
                    data: {
                        word: input.value
                    },
                    success: function (data) {
                        console.log(data);
                        const keyword_list = JSON.parse(data);
                        data_list =document.getElementById('keywords_list');
                        data_list.innerHTML="";
                        for(i=0;i<keyword_list.length;i++){
                            data_list.innerHTML +='<option value="'+keyword_list[i]+'">';
                        }
                        
                    },
                    error: function (data) {
                        console.log('An error occurred.');
                    },
                });
                
            }
        }
    // material
        function materialselect() {        // On selecting a material the sub material should be appear are those related to the selected material
            var selectmaterial = document.getElementById("selectmaterial");// get element
            var selectedValue = selectmaterial.options[selectmaterial.selectedIndex].value; //get the selected value
            var sub_material_element = document.getElementById("sub_material"); 
            sub_material_element.innerHTML='';//clear before print 
            for(i=0;i<all_sub_materials.length;i++){
                    if( all_sub_materials[i]['sub_material_material'] == selectedValue && selectedValue !=-1){// if this sub_material related to the selected material then print it
                        sub_material_element.innerHTML +="<option value='"+all_sub_materials[i]['sub_material_id']+"'>"+all_sub_materials[i]['sub_material_title']+"</option> ";
                    }else if(selectedValue ==-1){
                        sub_material_element.innerHTML +="<option value='"+all_sub_materials[i]['sub_material_id']+"'>"+all_sub_materials[i]['sub_material_title']+"</option> ";
                    }
                }     
        }
    // initial setup
        function initial_setup(selected_value){
            var selectedValue = selected_value;;
            var selectmaterial = document.getElementById("selectmaterial");// get element
            var sub_material_element = document.getElementById("sub_material"); 
            sub_material_element.innerHTML='';//clear before print 
            for(i=0;i<all_sub_materials.length;i++){
                    var selected ="";
                if( all_sub_materials[i]['sub_material_id'] == selectedValue){// if this sub_material related to the selected material then print it
                    selected ="selected"
                }
                sub_material_element.innerHTML +="<option "+selected+" value='"+all_sub_materials[i]['sub_material_id']+"'>"+all_sub_materials[i]['sub_material_title']+"</option> ";
            }     
        }
   
    </script>
