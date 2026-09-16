part of 'home_bloc.dart';

sealed class HomeState extends Equatable {
  const HomeState();
  @override
  List<Object?> get props => [];
}

/// Skeleton loader (Étape 2.4) — uniquement au premier chargement.
class HomeLoading extends HomeState {
  const HomeLoading();
}

class HomeLoaded extends HomeState {
  final HomeSummary summary;
  const HomeLoaded(this.summary);

  @override
  List<Object?> get props => [summary];
}

/// Étape 2.3 : distinct d'un compteur à 0 — état d'échec explicite, avec
/// action "réessayer", jamais un 0 silencieux.
class HomeError extends HomeState {
  final String message;
  const HomeError(this.message);

  @override
  List<Object?> get props => [message];
}
