<?php

interface IBuqueRepository
{
    public function find($id): Buque;
    public function findAll(): array;
    public function create(Buque $buque): Buque;
    public function update(Buque $buque): void;
    public function delete($id): void;
}