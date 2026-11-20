<?php

namespace Tests\Feature;

use Tests\TestCase;

class TransactionHistoryTest extends TestCase
{
    public function test_transactions_route_renders_history_filters_and_receipt(): void
    {
        $response = $this->get('/transactions');

        $response->assertOk()
            ->assertViewIs('transactions')
            ->assertSee('Transaction History')
            ->assertSee('Customer or transaction no.')
            ->assertSee('Swedish Massage')
            ->assertSee('Therapeutic Massage')
            ->assertSee('Ventosa')
            ->assertSee('30 Minutes Massage')
            ->assertSee('View Receipt')
            ->assertSee('Amount Paid')
            ->assertSee('Print');
    }
}
