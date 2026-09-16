import 'package:mobile_technicien/core/error/failure.dart';

/// Résultat léger (équivalent Either minimal) pour éviter une dépendance
/// supplémentaire (dartz) pour un seul usage : succès typé OU Failure.
sealed class Result<T> {
  const Result();

  R when<R>({
    required R Function(T data) success,
    required R Function(Failure failure) failure,
  }) {
    final self = this;
    if (self is Success<T>) return success(self.data);
    if (self is Failed<T>) return failure(self.failure);
    throw StateError('Unreachable');
  }
}

class Success<T> extends Result<T> {
  final T data;
  const Success(this.data);
}

/// Nommé `Failed`, pas `Error` — `Error` est déjà un type de base de
/// `dart:core` (superclasse de `StateError`, etc.) ; le masquer localement
/// aurait été source de confusion pour tout code futur qui l'importerait.
class Failed<T> extends Result<T> {
  final Failure failure;
  const Failed(this.failure);
}
