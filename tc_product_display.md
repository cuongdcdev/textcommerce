# tc\_product\_display #

A collection of tags to display product information.

### 

<txp:tc\_price />

 ###

This tag displays the product price. It defaults to whatever the store currency is set to or, if no store currency has been set, USD.

**Attributes**

  * currency  Must be a valid [ISO 4217](http://www.xe.com/iso4217.php) currency code. Defaults to 'USD' if no code has been specified in store preferences. Not required.

### 

<txp:tc\_weight />

 ###

This tag displays the product weight along with the proper weight units. It defaults to whatever the store weight units are set to. "Imperial" and "Metric" are the only two options.

**Attributes**

**units Must be either 'metric' or 'imperial'. Not required.**

### 

<txp:tc\_sku />

 ###

This tag displays the product sku number. No attributes.

### 

<txp:tc\_stock />

 ###

This tag displays how many items remain in stock. No attributes.

### 

<txp:tc\_vendor />

 ###

This tag displays the product vendor name. No attributes.

### 

<txp:tc\_product\_image\_1 />

 ###

This tag create an <img> tag of the first product image.<br>
<br>
<b>Attributes</b>

<ul><li>size – Valid values are 'small', 'meduim', or 'large'. Defaults to 'meduim'. Not required.<br>
</li><li>class – Specifies a stylesheet class for the <img> tag. Not required.<br>
</li><li>alt - Specifies alt text for the <img> tag. Defaults to product name. Not required.</li></ul>

<h3>

<txp:tc_product_image_2 />

</h3>

Same as  <br>
<br>
<txp:tc_product_image_1 /><br>
<br>
 .<br>
<br>
<h3>

<txp:tc_product_image_3 />

</h3>

Same as  <br>
<br>
<txp:tc_product_image_1 /><br>
<br>
 .<br>
<br>
<h3>

<txp:tc_product_image_4 />

</h3>

Same as  <br>
<br>
<txp:tc_product_image_1 /><br>
<br>
 .<br>
<br>
<br>
<br>
<br>
<br>
<br>
