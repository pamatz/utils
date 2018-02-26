<?php

namespace App\Classes;

use Illuminate\Support\Facades\Storage;

class Replacer
{

    protected $original_path;

    protected $content;

    protected $path;

    protected $originalName;

    protected $path_save;

    protected $original_content;

    public function __construct($file_path = null, $storage = false)
    {
        if ($file_path) {
            if ($storage) {
                $this->original_path = Storage::path($file_path);
            } else {
                $this->original_path = $file_path;
            }
            $this->originalName = basename($this->original_path);
            $this->content = file_get_contents($this->original_path);
        }
        $this->path = 'files/';
    }

    public function getOriginalPath()
    {
        return $this->original_path;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getPathSaved()
    {
        return $this->path_save;
    }

    public function getString($regex, $index = 0)
    {
        $result = preg_match($regex, $this->content, $matchs);
        if ($result) {
            return $matchs[$index];
        }

        return false;
    }

    public function changeDate($date)
    {
        $this->content = substr_replace($this->content, $date, 27, 4);

        return $this;
    }

    public function changeCodeProvedor($code_provedor)
    {
        $this->content = substr_replace($this->content, $code_provedor, 34, 8);

        return $this;
    }

    public function save($path = null)
    {
        if ($path) {
            $this->path = $path;
        }
        Storage::put($this->path . $this->originalName, $this->content);
        $this->path_save = Storage::path($this->path . $this->originalName);

        return $this;
    }

    public function deleteFileSaved()
    {
        Storage::delete($this->path . $this->originalName);

        return $this;
    }

    public function sum_products()
    {
        $this->original_content = $this->content;
        $this->content = substr_replace($this->content, '', 102);
        preg_match_all('/([A-Z]{2}[0-9]{6})\s+(([A-Z]{2}[0-9]{13}[A-Z]{2}\s+[0-9]{10}PZA[0-9]{17})\s+)+/', $this->original_content, $matches, PREG_OFFSET_CAPTURE);
        collect($matches[0])->each(function ($cajon, $index) use ($matches) {
            //Se obtienen cajones y se incrementan (se almacenan)
            preg_match('/([A-Z]{2})([0-9]+)/', $matches[1][$index][0], $cajon);
            $new_cajon = $cajon[1] . str_pad($cajon[2] + $index, 6, '0', STR_PAD_LEFT);
            preg_match_all('/([A-Z]{2}[0-9]{13}[A-Z]{2})\s+([0-9]{10}PZA[0-9]{17})\s+/', $matches[0][$index][0], $productos);
            $codigos = collect($productos[1]);
            $piezas = collect($productos[2]);
            $prev_codigo = null;
            $data_block = collect();
            $codigos->each(function ($codigo, $index) use ($piezas, &$prev_codigo, &$data_block) {
                if ($codigo == $prev_codigo) {
                    $data_block = $data_block->slice(0, - 1);
                    preg_match('/([0-9]{10})PZA([0-9]{17})/', $piezas[$index], $array_pieza_actual);
                    preg_match('/([0-9]{10})PZA([0-9]{17})/', $piezas[$index - 1], $array_pieza_prev);
                    $total = $array_pieza_actual[1] + $array_pieza_prev[1];
                    $new_pieza = str_pad($total, 10, '0', STR_PAD_LEFT) . 'PZA' . $array_pieza_actual[2];
                    $data_block->push(['codigo' => $codigo, "pieza" => $new_pieza]);
                    $prev_codigo = $codigo;
                } else {
                    $data_block->push(['codigo' => $codigo, "pieza" => $piezas[$index]]);
                    $prev_codigo = $codigo;
                }
            });
            $this->content .= $new_cajon . "\r\n";
            $data_block->each(function ($item) {
                $this->content .= $item['codigo'] . str_pad('', 59, ' ') . $item['pieza'] . "\r\n";
            });
        });

        return $this;
    }
}