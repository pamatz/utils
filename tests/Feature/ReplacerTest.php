<?php

namespace Tests\Feature;

use App\Classes\Replacer;
use Tests\TestCase;

class ReplacerTest extends TestCase
{
    protected $file_path;
    protected $replacer;

    public function setUp()
    {
        $this->file_path = base_path('tests/Resources/error_file_replacer.txt');
        $this->replacer = new Replacer($this->file_path);
        parent::setUp();
    }

    /** @test */
    public function if_get_string_from_file()
    {
        $string = $this->replacer->getString('/([A-Z])\w+/', 0);
        $this->assertEquals('HDA61432018021320180213201802200700122952', $string);
    }

    /** @test */
    public function is_date_changed()
    {
        $date = "1210";
        $this->replacer->changeDate($date);
        $first_string = $this->replacer->getString('/([A-Z])\w+/', 0);
        $this->assertEquals('HDA61432018021320180213201812100700122952', $first_string);
    }

    /** @test */
    public function is_code_provedor_changed()
    {
        $code_provedor = '01000001';
        $this->replacer->changeCodeProvedor($code_provedor);
        $first_string = $this->replacer->getString('/([A-Z])\w+/', 0);
        $this->assertEquals('HDA614320180213201802132018022007001000001', $first_string);
    }

    /** @test */
    public function if_content_was_set()
    {
        $new_content = "New content";
        $this->replacer->setContent($new_content);
        $this->assertEquals($new_content, $this->replacer->getContent());
    }

    /** @test */
    public function is_file_saved_with_default_path()
    {
        $this->replacer->save();
        $this->assertFileExists($this->replacer->getPathSaved());
    }

    /** @test */
    public function is_get_path_saved()
    {
        $this->replacer->save();
        $this->assertEquals(storage_path('app/files/error_file_replacer.txt'), $this->replacer->getPathSaved());
    }

    /** @test */
    public function is_file_deleted_with_default_path()
    {
        $this->replacer->save()->deleteFileSaved();
        $this->assertFileNotExists($this->replacer->getPathSaved());
    }

    /** @test */
    public function is_file_saved_with_custom_path()
    {
        $this->replacer->save('custom_path/path/algo/');
        $this->assertFileExists($this->replacer->getPathSaved());
    }

    /** @test */
    public function is_file_deleted_with_custom_path()
    {
        $this->replacer->save('custom_path/path/algo/')->deleteFileSaved();
        $this->assertFileNotExists($this->replacer->getPathSaved());
    }

    /** @test */
    public function if_sum_products()
    {
        $file_path = base_path('tests/Resources/error_sum_replace.txt');
        $replacer = new Replacer($file_path);
        $code_provedor = '01000001';
        $replacer->changeCodeProvedor($code_provedor)
            ->changeDate('0226')
            ->sum_products()->save();
        $this->assertFileEquals(base_path('tests/Resources/error_sum_ok.txt'), $replacer->getPathSaved());
        $replacer->deleteFileSaved();
    }

}
