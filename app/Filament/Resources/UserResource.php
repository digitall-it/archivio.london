<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
// use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
// use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
// use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    TextInput::make('name')
                        ->label('Name')
                        ->required(),
                    TextInput::make('email')
                        ->label('Email')
                        ->required(),
                    TextInput::make('password')->password()
                        ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                        ->dehydrateStateUsing(static function ($state) use ($form) {
                            if (! empty($state)) {
                                return Hash::make($state);
                            }

                            return User::find($form->getColumns())?->password;

                        }),
                    // ->afterStateUpdated(fn ($state, callable $set) => $set('password_confirmation', $state)),
                    TextInput::make('password_confirmation')
                        ->password()
                        ->label('Conferma Password')
                        ->required(fn ($state, $get) => ! empty($get('password')))
                        ->dehydrated(false)
                        ->same('password'),
                ]),
                Section::make([
                    // Select::make('roles')->multiple()->relationship('roles', 'name')->label('Ruoli'),
                    CheckboxList::make('roles')
                        ->label('Ruoli')
                        ->relationship('roles', 'name'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    //    public static function getRelations(): array
    //    {
    //        return [
    //            //
    //        ];
    //    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
