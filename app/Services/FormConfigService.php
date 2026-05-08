<?php

namespace App\Services;

use App\Models\FormConfig;
use App\Models\TahunPenerimaan;

class FormConfigService
{
    public function getOrCreate(TahunPenerimaan $tahun): FormConfig
    {
        $allFields = FormConfig::allConfigurableFields();

        return FormConfig::firstOrCreate(
            ['tahun_penerimaan_id' => $tahun->id],
            [
                'field_configs' => array_fill_keys(array_keys($allFields), true),
                'is_locked'     => false,
            ]
        );
    }

    public function update(FormConfig $config, array $activeFields): void
    {
        if ($config->is_locked) {
            throw new \RuntimeException(
                'Konfigurasi formulir sudah terkunci karena sudah ada peserta yang submit. Tidak dapat diubah.'
            );
        }

        $fieldConfigs = [];
        foreach (array_keys(FormConfig::allConfigurableFields()) as $key) {
            // FIXED_FIELDS selalu true, tidak bisa di-override
            if (in_array($key, FormConfig::FIXED_FIELDS)) {
                $fieldConfigs[$key] = true;
            } else {
                $fieldConfigs[$key] = in_array($key, $activeFields);
            }
        }

        $config->update(['field_configs' => $fieldConfigs]);
    }

    public function lock(FormConfig $config): void
    {
        if (!$config->is_locked) {
            $config->update(['is_locked' => true]);
        }
    }
}
