<?php

namespace App\Traits;

trait CosineSimilarityTrait
{
    public function similarity(array $vec1, array $vec2)
    {
        $dotProduct = $this->dotProduct($vec1, $vec2);
        $normVec1 = $this->norm($vec1);
        $normVec2 = $this->norm($vec2);

        if ($normVec1 * $normVec2 == 0) {
            return 0;
        }

        return $dotProduct / ($normVec1 * $normVec2);
    }

    private function dotProduct(array $vec1, array $vec2)
    {
        $dotProduct = 0;

        foreach ($vec1 as $key => $value) {
            if (isset($vec2[$key])) {
                $dotProduct += $value * $vec2[$key];
            }
        }

        return $dotProduct;
    }

    private function norm(array $vec)
    {
        $sum = 0;

        foreach ($vec as $value) {
            $sum += $value * $value;
        }

        return sqrt($sum);
    }
}
