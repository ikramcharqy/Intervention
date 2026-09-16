import 'package:equatable/equatable.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_technicien/features/chantier/domain/entities/chantier.dart';
import 'package:mobile_technicien/features/chantier/domain/usecases/get_chantier_detail_usecase.dart';

part 'chantier_detail_event.dart';
part 'chantier_detail_state.dart';

class ChantierDetailBloc extends Bloc<ChantierDetailEvent, ChantierDetailState> {
  final int chantierId;
  final GetChantierDetailUsecase getChantierDetailUsecase;

  ChantierDetailBloc({required this.chantierId, required this.getChantierDetailUsecase})
      : super(const ChantierDetailLoading()) {
    on<ChantierDetailRequested>(_onRequested);
  }

  Future<void> _onRequested(ChantierDetailRequested event, Emitter<ChantierDetailState> emit) async {
    emit(const ChantierDetailLoading());
    final result = await getChantierDetailUsecase(chantierId);
    result.when(
      success: (chantier) => emit(ChantierDetailLoaded(chantier)),
      failure: (f) => emit(ChantierDetailError(f.message)),
    );
  }
}
