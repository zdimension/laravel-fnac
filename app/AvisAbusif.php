<?php

namespace App;

class AvisAbusif extends ModelMultiplePrimary
{
    protected $table = "t_j_avisabusif_ava";
    protected $primaryKey = ["avi_id", "adh_id"];
    public $timestamps = false;
}
