<?php

function ctrbIsPremium()
{
    return CTRB_HAS_PRO ? cb_fs()->can_use_premium_code() : false;
}
