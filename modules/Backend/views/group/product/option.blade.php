@if (!empty($optionValue))
   <div id="div-option-{{ $optionId }}" class="option-value">
       <div class="row">
           <div class="col-md-6">
               <div class="form-group">
                   {{Form::select('option_id['.$optionId.'][option_type]', $optionTypeSelect,
                   $optionType, ['class'=>'select2 form-control', 'id'=>'option-type-'.$optionId])}}
               </div>
           </div>
           <div class="col-md-5"></div>
           <div class="col-md-1">
               <div class="option-trash option-trash-parent remove-option" attr-option-id="{{ $optionId }}">
                   <i class="fa fa-trash" aria-hidden="true"></i>
               </div>
           </div>
       </div>
       <table class="table table-bordered table-option-value">
           <thead>
               <tr>
                   <th class="drag-option"><i class="fa fa-bars" aria-hidden="true"></i></th>
                   <th>{{ __('Backend.Lang::lang.field.name') }}</th>
                   <th>{{ __('Backend.Lang::lang.field.price') }}</th>
                   <th colspan="2">{{ __('Backend.Lang::lang.field.type') }}</th>
               </tr>
           </thead>
           <tbody id="tbody-option-value-{{ $optionId }}" class="tbody-option-value">
               @php
               $index = 1;
               @endphp
               @foreach ($optionValue as $row)
               <tr class="">
                   <td width="5%" class="drag-option">
                       <i class="fa fa-bars" aria-hidden="true"></i>
                       <input type="hidden" name="product_to_option_id_update[]" value="0" />
                       <input type="hidden" name="option_id[{{$optionId}}][value][{{$index}}][id_update]" value="0" />
                   </td>
                   <td width="10%">
                       <div class="form-group">
                           {{Form::select('option_id['.$optionId.'][value]['.$index.'][id]',
                           $optionValueSelect, $row->id,
                           ['class'=>'select2 form-control', 'id'=>'option-value-'.$row->id])}}
                       </div>
                   </td>
                   <td width="40%">
                       <input type="number" class="form-control" value="{{ $row->price }}"
                              name="option_id[{{$optionId}}][value][{{$index}}][price]" />
                   </td>
                   <td width="40%">
                       <div class="form-group">
                           {{Form::select('option_id['.$optionId.'][value]['.$index.'][type]',
                           $optionValueTypeSelect, $row->type,
                           ['class'=>'select2 form-control', 'id'=>'option-value-type-'.$row->id])}}
                           <input type="hidden" class="option_value_sort" value="0"
                                  name="option_id[{{ $optionId }}][value][{{$index}}][sort_order]"/>
                       </div>
                   </td>
                   <td width="5%" class="not-cursor-grab">
                       <div class="option-trash remove-option-value" product-to-option="0">
                           <i class="fa fa-trash" aria-hidden="true"></i>
                       </div>
                   </td>
               </tr>
               @php
               $index++;
               @endphp
               @endforeach
           </tbody>
           <tfoot>
               <tr>
                   <td colspan="4"></td>
                   <td>
                       <div class="option-plus" attr-option-id="{{ $optionId }}">
                           <i class="fa fa-plus-square" aria-hidden="true"></i>
                       </div>
                       <div class="option-value-select-json" style="display: none">{{ json_encode($optionValueSelect) }}</div>
                   </td>
               </tr>
           </tfoot>
       </table>
   </div>
@endif