<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "guiastur/Application/UseCases/GetNextTurno/Dto/GetNextTurnoRequest.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "guiastur/Application/UseCases/GetNextTurno/Dto/GetNextTurnoResponse.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "guiastur/Application/UseCases/UseTurno/Dto/UseTurnoRequest.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "guiastur/Application/UseCases/UseTurno/Dto/UseTurnoResponse.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "guiastur/Application/UseCases/GetUsuarioById/Dto/GetUsuarioByIdRequest.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "guiastur/Application/UseCases/GetUsuarioById/Dto/GetUsuarioByIdResponse.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "guiastur/Application/Contracts/Actions/Queries/IGetNextTurnoQuery.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "guiastur/Application/Contracts/Actions/Commands/IUseTurnoCommand.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "guiastur/Application/Contracts/Actions/Queries/IGetUsuarioByIdQuery.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "guiastur/Application/Contracts/UseCases/IUseTurnoUseCase.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "guiastur/Application/Exceptions/ValidateUseTurnoException.php";


class UseTurnoUseCase implements IUseTurnoUseCase {
    private $getGetNextTurnoQuery;
    private $getUsuarioByIdQuery;
    private $useTurnoCommand;

    public function __construct(IGetUsuarioByIdQuery $getUsuarioByIdQuery
                                , IGetNextTurnoQuery $getNextTurnoQuery
                                , IUseTurnoCommand $useTurnoCommand) 
    {
        $this->getGetNextTurnoQuery = $getNextTurnoQuery;
        $this->useTurnoCommand = $useTurnoCommand;
        $this->getUsuarioByIdQuery = $getUsuarioByIdQuery;
    }

    public function useTurno(UseTurnoRequest $request): UseTurnoResponse{
        $nextTurnoRequest = new GetNextTurnoRequest($request->getAtencionId());
        $nextTurnoResponse = $this->getGetNextTurnoQuery->handler($nextTurnoRequest);
        if($nextTurnoResponse->getId() !=  $request->getTurnoId()){
            throw new ValidateUseTurnoException("Uso de turno rechazado, Proximo Turno Numero: ".$nextTurnoResponse->getNumero()."");
        }    
        
        $usuarioByIdRequest = new GetUsuarioByIdRequest($request->getUsuarioUsoId());
        $usuarioByIdResponse = $this->getUsuarioByIdQuery->handler($usuarioByIdRequest);
        if($usuarioByIdResponse->getRolNombre() !== "Super Usuario" 
            && $usuarioByIdResponse->getRolNombre() !== "Supervisor" 
            && $usuarioByIdResponse->getId() != $nextTurnoResponse->getGuia()->getUserId())
        {
            throw new ValidateUseTurnoException("No tiene permisos para registar el uso del turno ".$nextTurnoResponse->getNumero() ." del Guia ". $nextTurnoResponse->getGuia()->getNombre());
        }  

        return $this->useTurnoCommand->handler($request);
    }
}
         