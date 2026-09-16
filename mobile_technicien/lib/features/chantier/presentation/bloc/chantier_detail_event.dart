part of 'chantier_detail_bloc.dart';

sealed class ChantierDetailEvent extends Equatable {
  const ChantierDetailEvent();

  @override
  List<Object?> get props => [];
}

class ChantierDetailRequested extends ChantierDetailEvent {
  const ChantierDetailRequested();
}
