<script type="text/javascript">

$(document).ready(function(){
    $('#selecctall').click(function(event) {  //on click
        if(this.checked) { // check select status
            $('.checkbox1').each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "checkbox1"              
            });
        }else{
            $('.checkbox1').each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "checkbox1"                      
            });        
        }
    });
   

    $('#storeForm').find(':checkbox[name^="catIds[]"]').each(
        function () {
            $(this).prop("checked", ($.inArray($(this).val(), checkedValues) != -1))
        }
    );

});

recursion = function(data, is_child) {
    var output = '';
    if (typeof is_child == 'undefined') {
        is_child = false;
    }

    // start:ul (only one of these)
    if (is_child == false) {
        output += '<ul id="org">\n';
    }

    // end:ul
    var len = data.length;
    for (var i = 0; i < len; i++)
    {
            // If this is a child loop, and its the first iteration, it
        // has a special case:
        // <ul>
        // <li>first</li>
        // <li>second<ul>
        var first_child_item = false;
        if (is_child == true && i == 0) {
            first_child_item = true;
        }

        // open:main wrapper
        if (first_child_item == true) {
            output += '<ul class="first_child_item">\n';
            output += '<li>' + data[i].node_id + '</li>\n';
            continue;
        } else {
            if (is_child) {
                // When there is a child with another beside it
                output += '<li>' + data[i].node_id;
            } else {
                // the main low level call
                output += '<ul class="low_level">\n';
                output += '<li>' + data[i].node_id;
            }
        }

        // open:children seek
        if (data[i].children.length > 0)
        {
            output += recursion(data[i].children, true);
        }

        // close pending tags
        if (typeof data[i+1] == 'undefined')
        {
            for (var j = 0; j < i; j++) {
                output += '</li>\n';
                output += '</ul>\n';
            }
        }
    }
    // end main:ul (only one of these)
    output += '</ul>\n';

    return output;
}

/********************************************************************************************
* sample data

var data = [
    {
        "node_id":1,
        "children":[]
    },
    {
        "node_id":2,
        "children":[]
    },
    {
        "node_id":3,
        "children":[
            {
                "node_id":4,
                "children":[]
            },
            {
                "node_id":5,
                "children":[
                    {
                        "node_id":6,
                        "children":[]
                    },
                    {
                        "node_id":7,
                        "children":[]
                    }
                ]
            }
       ]
    }
];

*************************************************************************************/
</script>
