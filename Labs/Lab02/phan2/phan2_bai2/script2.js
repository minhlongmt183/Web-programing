function caculate(num1, num2)
{
    res = 0;
    a = parseInt(num1);
    b = parseInt(num2);

    if (document.getElementById("choice-add").checked)
        res = a + b;
    else if (document.getElementById("choice-sub").checked)
        res = a - b;
    else if (document.getElementById("choice-mul").checked)
        res = a * b;
    else if (document.getElementById("choice-div").checked)
        res = a / b;

    document.getElementById("result").value = res;
}

function power(num){
    res = 1;
    numInt = parseInt(num)

    for(i = 1; i <= numInt; i++)
        res = res * i;
    

    document.getElementById("power-result").value = res;

}