<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->unique(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Select::make('role')
                    ->options(['admin' => 'Admin', 'staff' => 'Staff', 'user' => 'User'])
                    ->default('user')
                    ->required(),
                Toggle::make('is_active')
                    ->required()
                    ->default(true),    
            ]);
    }
}
