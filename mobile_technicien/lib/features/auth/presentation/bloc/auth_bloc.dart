import 'package:equatable/equatable.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_technicien/features/auth/domain/entities/technicien.dart';
import 'package:mobile_technicien/features/auth/domain/usecases/get_current_user_usecase.dart';
import 'package:mobile_technicien/features/auth/domain/usecases/login_usecase.dart';
import 'package:mobile_technicien/features/auth/domain/usecases/logout_usecase.dart';
import 'package:mobile_technicien/features/auth/domain/repositories/auth_repository.dart';

part 'auth_event.dart';
part 'auth_state.dart';

class AuthBloc extends Bloc<AuthEvent, AuthState> {
  final LoginUsecase loginUsecase;
  final GetCurrentUserUsecase getCurrentUserUsecase;
  final LogoutUsecase logoutUsecase;
  final AuthRepository repository;

  AuthBloc({
    required this.loginUsecase,
    required this.getCurrentUserUsecase,
    required this.logoutUsecase,
    required this.repository,
  }) : super(const AuthInitial()) {
    on<AuthCheckRequested>(_onCheckRequested);
    on<AuthLoginRequested>(_onLoginRequested);
    on<AuthLogoutRequested>(_onLogoutRequested);
  }

  Future<void> _onCheckRequested(AuthCheckRequested event, Emitter<AuthState> emit) async {
    emit(const AuthLoading());

    if (!await repository.isAuthenticated()) {
      emit(const AuthUnauthenticated());
      return;
    }

    final result = await getCurrentUserUsecase();
    result.when(
      success: (user) => emit(AuthAuthenticated(user)),
      failure: (f) => emit(AuthUnauthenticated(message: f.message)),
    );
  }

  Future<void> _onLoginRequested(AuthLoginRequested event, Emitter<AuthState> emit) async {
    emit(const AuthLoading());
    final result = await loginUsecase(email: event.email, password: event.password);
    result.when(
      success: (user) => emit(AuthAuthenticated(user)),
      failure: (f) => emit(AuthUnauthenticated(message: f.message)),
    );
  }

  Future<void> _onLogoutRequested(AuthLogoutRequested event, Emitter<AuthState> emit) async {
    await logoutUsecase();
    emit(const AuthUnauthenticated());
  }
}
