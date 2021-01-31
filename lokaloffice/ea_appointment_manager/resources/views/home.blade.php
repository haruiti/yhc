

@extends('layouts.app')
{{-- @include('Includes.head') --}}
@section('content')

@php date_default_timezone_set('America/Sao_Paulo') @endphp
<table id="datatable-corretivas" class="table table-striped table-bordered table" cellspacing="0" width="100%">
    <thead class="thead-th">
        <tr>
            <th>Dia</th>
            <th>Semana</th>
            <th>Hora Inicial</th>
            <th>Hora Final</th>
            <th>Sala</th>
            <th>Total de Horas</th>
            <th>Data limite p/ cancelamento</th>
            <th>Cancelar/Editar</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($users))

        @foreach($users as $user => $value)
        <tr>
            <td class="td" data-th="Dia:&nbsp&nbsp">
                <span >
                {{ date('d/m/Y', strtotime(str_replace('-', '/', $value->start_datetime))) }}

                </span>
            </td>
            <td class="td" data-th="Semana:&nbsp&nbsp">
                <span>
                    @php $dayOfWeek= date("l", strtotime($value->start_datetime))  @endphp
                    @if($dayOfWeek=='Monday')
                        Segunda
                    @elseif ($dayOfWeek=='Tuesday')
                        Terça
                    @elseif ($dayOfWeek=='Wednesday')
                        Quarta
                    @elseif ($dayOfWeek=='Thursday')
                        Quinta
                    @elseif ($dayOfWeek=='Friday')
                        Sexta
                    @elseif ($dayOfWeek=='Saturday')
                        Sábado
                    @elseif ($dayOfWeek=='Sunday')
                        Domingo
                    @endif
                </span>
            </td>
            <td class="td" data-th="Hora Inicial:&nbsp&nbsp">
                <span>
                {{ date("H:i",strtotime($value->start_datetime)) }}
                </span>
            </td>
            <td class="td" data-th="Hora Final:&nbsp&nbsp">
                <span>
                {{ date("H:i",strtotime($value->end_datetime)) }}
                </span>
            </td>
            <td class="td" data-th="Dia:&nbsp&nbsp">
                <span >
                {{ $value->last_name }}
                </span>
            </td>
            <td class="td" data-th="Total de Horas:&nbsp&nbsp">
                <span>
                {{ $value->name }}
                </span>
            </td>

            @if(strtotime('-8 hour',strtotime($value->start_datetime)) == strtotime('now'))
            <td class="td" data-th="Data limite p/ cancelamento:&nbsp&nbsp" style="color: green;">
                <span>
            @elseif(strtotime('-8 hour',strtotime($value->start_datetime)) > strtotime('now'))
            <td class="td" data-th="Data limite p/ cancelamento:&nbsp&nbsp" style="color: green;">
                <span>
             @elseif(strtotime('-8 hour',strtotime($value->start_datetime)) < strtotime('now'))
            <td class="td" data-th="Data limite p/ cancelamento:&nbsp&nbsp" style="color: red;">
                <span>
            @endif
                {{ date('d/m/Y H:i', strtotime(str_replace('-', '/',date('Y-m-d H:i',strtotime('-8 hour',strtotime($value->start_datetime)))))) }}
                </span>
            </td>
            <td class="td">
                <button type="button" title="Editar" data-toggle="modal" data-target="#modalCorretivas"
                    class="btn btn-primary" id="btn_getDados" data-id="{{ $value->hash }}"><i
                        class="fas fa-edit"></i></button>

                <button type="button" {{ (($value->hash == 'cancelado') ? "disabled" : "") }} title="Cancelar"
                    class="btn btn-danger btn_setStatus" data-id="{{ $value->hash  }}" data-status="cancelado">
                    <i class="fas fa-times-circle"></i>
                </button>

            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
<div id="corretiva-detail-modal"></div>
@endsection

<script>

    var x = window.matchMedia("(max-width: 640px)")
    if (x.matches) { // If media query matches

        $('.nav_menu').hide();

        $( document ).ready(function() {

            $('#datatable-corretivas').removeClass('nowrap');

            if(<?php echo count($users); ?> > 1){
                var tamanho = "400px";
            }else{
                var tamanho = "";
            }
            $('#datatable-corretivas').DataTable({
                dom: 'Bfrtip',
                buttons: [],
                paging:         false,
                lengthMenu: [[5,10,25, 50, 100, -1], [5,10,25, 50, 100, "Tudo"]],
                fixedColumns: {
                    leftColumns: 1
                },
                language: {
                            "lengthMenu": "Exibindo _MENU_ Registros por página",
                            "zeroRecords": "Nenhum registro encontrado",
                            "info": "Mostrando página _PAGE_ de _PAGES_",
                            "infoEmpty": "Nenhum registro encontrado",
                            "sSearch": "Pesquisar: ",
                            "oPaginate": {
                                "sNext": "Próximo",
                                "sPrevious": "Anterior",
                                "sFirst": "Primeiro",
                                "sLast": "Último"
                            },
                            "infoFiltered": "(filtrados de um total de _MAX_ registros)"
                            }
                });
        });

    } else {
        $( document ).ready(function() {

            $('#datatable-corretivas').addClass('nowrap');

        if(<?php echo count($users); ?> > 1){
            var tamanho = "400px";
        }else{
            var tamanho = "";
        }
                $('#datatable-corretivas').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'csv',
                            text: 'CSV',
                            title: "Hersil Corretivas"
                        },
                        {
                            extend: 'excel',
                            text: 'XLSX',
                            title: "Hersil Corretivas"
                        }
                    ],
                    scrollY:        tamanho,
                    scrollX:        true,
                    paging:         false,
                    lengthMenu: [[5,10,25, 50, 100, -1], [5,10,25, 50, 100, "Tudo"]],
                    fixedColumns: {
                        leftColumns: 1
                    },
                    language: {
                                "lengthMenu": "Exibindo _MENU_ Registros por página",
                                "zeroRecords": "Nenhum registro encontrado",
                                "info": "Mostrando página _PAGE_ de _PAGES_",
                                "infoEmpty": "Nenhum registro encontrado",
                                "sSearch": "Pesquisar: ",
                                "oPaginate": {
                                    "sNext": "Próximo",
                                    "sPrevious": "Anterior",
                                    "sFirst": "Primeiro",
                                    "sLast": "Último"
                                },
                                "infoFiltered": "(filtrados de um total de _MAX_ registros)"
                                }
                });
            });
        }


</script>


