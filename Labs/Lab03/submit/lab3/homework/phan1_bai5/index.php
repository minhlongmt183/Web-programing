<!DOCTYPE html>
<html>
<head>
    <title>Caculator</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://code.jquery.com/jquery-latest.js"></script>
    <style type="text/css">
        table{
            background-color: #ABB1BA;
            margin: auto;
        }

        td{
            text-align: center;
        }
        #result{
            width: 500px;
            height: 80px;
        }
        .btn{
            width: 90px;
            height: 90px;
            margin: auto;
        }
        #equ{
            width: 165px;
            height: 165px;
        }
        #zero{
            width: 165px;
            height: 80px;
        }

    </style>

</head>
<body>
    <table>
        <tr>
            <td colspan="7"><input id="result" type="text"  disabled></td>
        </tr>
        <tr>
            <td><input type="button" class="btn" value="7"></td>
            <td><input type="button" class="btn" value="8"></td>
            <td><input type="button" class="btn" value="9"></td>
            <td><input type="button" class="btn" value="/"></td>
            <td><input type="button" class="btn" value="AC"></td>
            <td><input type="button" class="btn" value="C"></td>
        </tr>
        <tr>
            <td><input type="button" class="btn" value="4"></td>
            <td><input type="button" class="btn" value="5"></td>
            <td><input type="button" class="btn" value="6"></td>
            <td><input type="button" class="btn" value="*"></td>
            <td><input type="button" class="btn" value="!"></td>
            <td><input type="button" class="btn" value="1/x"></td>
        </tr>
        <tr>
            <td><input type="button" class="btn" value="1"></td>
            <td><input type="button" class="btn" value="2"></td>
            <td><input type="button" class="btn" value="3"></td>
            <td><input type="button" class="btn" value="-"></td>
            <td rowspan="2" colspan="2"><input id="equ" type="button" class="btn" value="="></td>
        </tr>
        <tr>
            <td colspan="2"><input type="button" id="zero" class="btn" value="0" ></td>
            <td><input type="button" class="btn" value="."></td>
            <td><input type="button" class="btn" value="+"></td>
        </tr>
    </table>
    <script type="text/javascript">
        var a = '',b = '', op = '';
        $(function(){
            $('input').click(function() {
                var v = $(this).val();
                switch(v){
                    case '+':
                    case '-':
                    case '*':
                    case '/':
                    case '!':
                         op = v
                    break;
                    case '=':
                        // submit data
                        onSubmit()
                    break;
                    case 'AC':
                    case 'C':
                        a = '';
                        b = '';
                        op = '';
                    break;
                    case '1/x':
                        op = 'inv';
                    break;
                    default:
                        if(op != ''){
                            // input B
                            b += v;
                        } else{
                            // input A
                            a += v;
                        }
                    break;
                }

                if (v == '1/x')
                    $('#result').val('1/' + a)
                else

                    $('#result').val(a  + op  + b)
                    

            })
        })


        function onSubmit(){
            console.log("log: " + a + op + b)
            $.post('cal.php', {
                'a': a,
                'op': op,
                'b': b
            }, function(data){
                console.log(data)
                if (op != 'inv')
                    $('#result').val(a + op + b + '=' + data);
                else
                    $('#result').val('1 / ' + a + '=' + data);

            })

        }

    </script>

</body>
</html>