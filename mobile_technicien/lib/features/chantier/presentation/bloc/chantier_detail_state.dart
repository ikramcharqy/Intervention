part of 'chantier_detail_bloc.dart';

sealed class ChantierDetailState extends Equatable {
  const ChantierDetailState();

  @override
  List<Object?> get props => [];
}

class ChantierDetailLoading extends ChantierDetailState {
  const ChantierDetailLoading();
}

class ChantierDetailError extends ChantierDetailState {
  final String message;
  const ChantierDetailError(this.message);

  @override
  List<Object?> get props => [message];
}

class ChantierDetailLoaded extends ChantierDetailState {
  final Chantier chantier;
  const ChantierDetailLoaded(this.chantier);

  @override
  List<Object?> get props => [chantier];
}
