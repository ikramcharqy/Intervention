part of 'rapport_detail_bloc.dart';

sealed class RapportDetailEvent extends Equatable {
  const RapportDetailEvent();

  @override
  List<Object?> get props => [];
}

class RapportDetailRequested extends RapportDetailEvent {
  const RapportDetailRequested();
}

class RapportPdfRequested extends RapportDetailEvent {
  const RapportPdfRequested();
}
