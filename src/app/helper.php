<?php

function breadcrumbs($crumbs)
{
    return view('partials.breadcrumbs', ['crumbs' => $crumbs])->render();
}

?>