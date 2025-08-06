<?php
namespace App\Filament\Resources\ToolRequestResource\Pages;

use App\Filament\Resources\ToolRequestResource;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use App\Mail\SendToolQuotation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\App;

class ViewToolRequest extends ViewRecord
{
    protected static string $resource = ToolRequestResource::class;



    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('send_quotation')
                ->label(__('filament.actions.send_quotation'))
                ->form([
                    TextInput::make('amount')
                        ->label(__('filament.fields.amount'))
                        ->numeric()
                        ->required(),
                    Select::make('account_id')
                        ->label(__('filament.fields.bank_account'))
                        ->relationship('account', 'bank_name')
                            ->native(false)
                        ->required(),
                    Textarea::make('note')
                        ->label(__('filament.fields.notes')),
                ])
                ->action(function (array $data) {
                    $record = $this->record;

                    $account = \App\Models\Account::findOrFail($data['account_id']);

                    // Update the relation
                    $record->update([
                        'account_id' => $account->id,
                        'status' => 'quoted',
                        'quotation_sent_at' => now(),
                    ]);

                    Mail::to($record->email)->send(new SendToolQuotation($record, $account, $data));

                    Notification::make()
                        ->title(__('filament.notifications.quotation_sent'))
                        ->success()
                        ->send();
                })
                ->visible(fn ($record) => $record->status === 'pending'),
        ];
    }

    public function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist->schema([
            \Filament\Infolists\Components\TextEntry::make('name')
                ->label(__('filament.fields.name')),
            \Filament\Infolists\Components\TextEntry::make('email')
                ->label(__('filament.fields.email')),
            \Filament\Infolists\Components\TextEntry::make('organization')
                ->label(__('filament.fields.organization')),
            \Filament\Infolists\Components\TextEntry::make('message')
                ->label(__('filament.fields.message'))
                ->columnSpanFull(),
            \Filament\Infolists\Components\TextEntry::make('status')
                ->label(__('filament.fields.status'))
                ->badge()
                ->formatStateUsing(fn($state) => __('filament.status.' . $state)),
            \Filament\Infolists\Components\TextEntry::make('quotation_sent_at')
                ->label(__('filament.fields.quotation_sent_at'))
                ->dateTime(),
            \Filament\Infolists\Components\TextEntry::make('tool.name')
                ->label(__('filament.fields.tool'))
                ->getStateUsing(fn ($record) => App::getLocale() === 'ar' ? $record->tool->name_ar : $record->tool->name_en),
            \Filament\Infolists\Components\TextEntry::make('created_at')
                ->label(__('filament.fields.created_at'))
                ->dateTime(),
        ]);
    }
}
