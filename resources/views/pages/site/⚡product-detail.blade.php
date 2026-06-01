<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
<x-page-banner
    tag="Product"
    tagIcon="deployed_code"
    title='Product <span style="color:#00b8db;">Detail</span>'
    :breadcrumbs="[['label'=>'Home','route'=>'home'],['label'=>'Products','route'=>'site.products'],['label'=>'Product']]"
    glowX="55%"
    glowX2="20%"
></x-page-banner>

    {{-- It always seems impossible until it is done. - Nelson Mandela --}}
</div>