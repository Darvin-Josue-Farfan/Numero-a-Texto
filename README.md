## Descripción
Clase de PHP para convertir un número entero (desde <b>0</b> hasta <b>10<sup>126</sup>-1 </b>) a texto. 

## Uso
```php
require './src/NumberToText.php';

```
## Conversión
```php
$number = new NumberToText($stringNumber, $femaleMode, $apocopeMode);
echo $number->getText();
```
Parámetros:
> $stringNumber: El número entero a tratar. <code>string|int</code> <code>opcional</code>

> $femaleMode: Indica si el texto esta en género femenino. <code>bool</code> <code>opcional</code> <code>Default: false</code>

> $apocopeMode: Apocope del uno. <code>bool</code> <code>opcional</code> <code>Default: false</code>

## getText()

Sin parámetros.
> retorna un <code>string</code>  número convertido a texto.

Nota: Si no especificó el <code>$stringNumber</code> en el constructor, entonces debe llamar primero a <code>setNumber()</code>

## setNumber($stringNumber)

Parámetros:
> $stringNumber: Indica/cambia el número entero a tratar. <code>string|int</code> <code>requerido</code>

no tiene valor de retorno, se debe llamar seguidamente a <code>getText()</code>

## femaleMode() - maleMode()
Sin parámetros ni valor de retorno.
> femaleMode() para establer el texto en femenino.
> maleMode() para establer el texto en masculino.

Deben ser invocadas antes de <code>getText()</code>

## setApocopeMode($apocopeMode)

Parámetros:

>$apocopeMode: Indica el apocope para el uno. <code>bool</code> <code>requerido</code>

Debe ser invocada antes de <code>getText()</code>

## Ejemplos
```php
$number = new NumberToText("81");
echo $number->getText(); // imprime: ochenta y uno
```

```php
$number = new NumberToText("200", true);
echo $number->getText(); // imprime: doscientas
```

```php
$number = new NumberToText("801", false, true);
echo $number->getText(); // imprime: ochocientos un
```

```php
$number = new NumerToText();
$number->femaleMode();
$number->setNumber("1509");
echo $number->getText(); // imprime: mil quinientas nueve
```

```php
$number = new NumberToText();
$number->setApocopeMode(true);
$number->setNumber("401");
echo $number->getText(); // imprime: cuatrocientos un
```
