<?php

namespace App;

enum HouseRole: int
{
    case HOST = 1;
    case RESIDENT = 2;
    case GUEST = 3;
}
