<tr class="div-item" attr-index="{{ $index }}" id="variant-tr-{{ $index }}"
    attr-property-str="{{ $form['property_string'] }}">
    <td>
        <textarea class="variant-json-data" id="variant-json-{{ $index }}"
                  name="variant[]" id-update="0"
                  style="display: none">{{ json_encode($form) }}</textarea>
        <span>{{ $strProperty }}</span>
    </td>
    <td><span class="price-variant">{{ $form['price_variant'] }}</span></td>
    <td><span class="qty-variant">{{ $form['qty_variant'] }}</span></td>
    <td class="remove-variant">
        <span><i class="fa fa-trash" aria-hidden="true"></i></span>
    </td>
</tr>