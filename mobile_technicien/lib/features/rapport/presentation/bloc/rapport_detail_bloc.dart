import 'dart:typed_data';

import 'package:equatable/equatable.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_technicien/features/rapport/domain/entities/rapport.dart';
import 'package:mobile_technicien/features/rapport/domain/usecases/get_rapport_by_intervention_usecase.dart';
import 'package:mobile_technicien/features/rapport/domain/usecases/get_rapport_pdf_bytes_usecase.dart';

part 'rapport_detail_event.dart';
part 'rapport_detail_state.dart';

class RapportDetailBloc extends Bloc<RapportDetailEvent, RapportDetailState> {
  final int interventionId;
  final GetRapportByInterventionUsecase getRapportByInterventionUsecase;
  final GetRapportPdfBytesUsecase getRapportPdfBytesUsecase;

  RapportDetailBloc({
    required this.interventionId,
    required this.getRapportByInterventionUsecase,
    required this.getRapportPdfBytesUsecase,
  }) : super(const RapportDetailLoading()) {
    on<RapportDetailRequested>(_onRequested);
    on<RapportPdfRequested>(_onPdfRequested);
  }

  Future<void> _onRequested(RapportDetailRequested event, Emitter<RapportDetailState> emit) async {
    emit(const RapportDetailLoading());
    final result = await getRapportByInterventionUsecase(interventionId);
    result.when(
      success: (rapport) => emit(RapportDetailLoaded(rapport)),
      failure: (f) => emit(RapportDetailError(f.message)),
    );
  }

  Future<void> _onPdfRequested(RapportPdfRequested event, Emitter<RapportDetailState> emit) async {
    final current = state;
    if (current is! RapportDetailLoaded) return;

    emit(current.copyWith(downloadingPdf: true, pdfBytes: null, pdfError: null));
    final result = await getRapportPdfBytesUsecase(current.rapport.id);
    result.when(
      success: (bytes) => emit(current.copyWith(downloadingPdf: false, pdfBytes: Uint8List.fromList(bytes))),
      failure: (f) => emit(current.copyWith(downloadingPdf: false, pdfError: f.message)),
    );
  }
}
