<?php

namespace Paysera\Component\Serializer\Tests\Fixtures\Accessor;

class Payment
{
    private $beneficiary;

    public function __construct(Payee $beneficiary)
    {
        $this->beneficiary = $beneficiary;
    }

    public function getBeneficiary()
    {
        return $this->beneficiary;
    }
}
