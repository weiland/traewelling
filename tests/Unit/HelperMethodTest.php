<?php

namespace Tests\Unit;

use Tests\TestCase;
use App;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HelperMethodTest extends TestCase
{
    
    use RefreshDatabase;

    public function testNumberFunction() {
        App::setLocale('de');
        
        // Thousands with a rounding decimal
        $this->assertEquals("1.000,23", number(1000.234));

        // Deal with small numbers
        $this->assertEquals("0,00", number(0));
        $this->assertEquals("0,00", number(0.001));
        $this->assertEquals("0,01", number(0.0099));

        // if n \in \N, add dummy nulls after the null.
        $this->assertEquals("1,00", number(1));

        // What about large numbers?
        $this->assertEquals("4.294.967.296,00", number(2**32));

        // What about negative numbers?
        $this->assertEquals("0,00", number(-0));
        $this->assertEquals("-1,00", number(-1));
        $this->assertEquals("-4.294.967.296,00", number(-1* 2**32));
    }

    public function testStationLink() {
        
        $user = $this->createGDPRAckedUser();
        $dom = new \DOMDocument;

        foreach([
            // First, start with something simple
            'Aachen Hbf' => 'Aachen Hbf',
            
            // Umlaute, Sonderzeichen?
            'Köln Messe/Deutz' => 'Köln Messe/Deutz',
            // Da kann man zwar hinfahren, es ist aber keine echte Station und
            // wenn man es versucht, gibts einen 404 Fehler von der DB-Rest-API.
            'Köln Messe/Deutz Gl.11-12' => 'Köln Messe/Deutz',

            // Extrem lange Stationen?
            'Frankfurt am Main Flughafen Fernbahnhof' => 'Frankfurt am Main Flughafen Fernbahnhof',
            
            // Ausland?
            'Wien Meidling' => 'Wien Meidling',
        ] as $name => $alternatedTo) {
            $link = stationLink($name);

            libxml_use_internal_errors(true);
            $dom->loadHTML($link);
            $links = $dom->getElementsByTagName('a');
            $this->assertEquals(count($links), 1);
            
            $response = $this->actingAs($user)
                            ->get($links[0]->getAttribute('href'));
            $response->assertSee('<input type="text" id="station-autocomplete" name="station" class="form-control" placeholder="Haltestelle"  value="' . $alternatedTo . '" >');
            $response->assertSee($alternatedTo . ' <small><i class="far fa-clock fa-sm"></i>');
        }
    }
}
